import React, { useEffect, useRef, useState } from 'react';
import tw from 'twin.macro';
import { Terminal } from 'xterm';
import { FitAddon } from 'xterm-addon-fit';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card from '@/components/elements/Card';
import Badge from '@/components/elements/Badge';
import Button from '@/components/elements/Button';
import ErrorState from '@/components/elements/ErrorState';
import Spinner from '@/components/elements/Spinner';
import { useLxcContainer } from '@/components/lxc/LxcContainerContext';
import getLxcConsole, { LxcConsoleDetails } from '@/api/lxc/getLxcConsole';
import 'xterm/css/xterm.css';

type ConnectionState = 'connecting' | 'open' | 'closed';

export default () => {
    const { container } = useLxcContainer();
    const ref = useRef<HTMLDivElement>(null);
    const terminal = useRef<Terminal | null>(null);
    const socket = useRef<WebSocket | null>(null);

    const [details, setDetails] = useState<LxcConsoleDetails | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [state, setState] = useState<ConnectionState>('connecting');
    const [attempt, setAttempt] = useState(0);

    useEffect(() => {
        setError(null);
        setDetails(null);
        setState('connecting');

        getLxcConsole(container.uuid)
            .then(setDetails)
            .catch((e) => setError(e?.message || 'Unable to request a console session for this container.'));
    }, [container.uuid, attempt]);

    useEffect(() => {
        if (!details || !details.url || !ref.current) return;
        if (!/^wss?:\/\//i.test(details.url)) return;

        const term = new Terminal({
            fontSize: 13,
            fontFamily: 'Menlo, Consolas, monospace',
            cursorBlink: true,
            theme: { background: '#0b0e14' },
        });
        const fit = new FitAddon();
        term.loadAddon(fit);
        term.open(ref.current);
        fit.fit();
        terminal.current = term;

        const ws = new WebSocket(details.url);
        ws.binaryType = 'arraybuffer';
        socket.current = ws;

        ws.onopen = () => setState('open');
        ws.onclose = () => setState('closed');
        ws.onerror = () => setState('closed');
        ws.onmessage = (event) => {
            if (typeof event.data === 'string') {
                term.write(event.data);
            } else {
                term.write(new Uint8Array(event.data as ArrayBuffer));
            }
        };

        const disposable = term.onData((data) => {
            if (ws.readyState === WebSocket.OPEN) {
                ws.send(data);
            }
        });

        const onResize = () => fit.fit();
        window.addEventListener('resize', onResize);

        return () => {
            window.removeEventListener('resize', onResize);
            disposable.dispose();
            ws.close();
            term.dispose();
            terminal.current = null;
            socket.current = null;
        };
    }, [details]);

    const isWebsocket = !!details?.url && /^wss?:\/\//i.test(details.url);

    return (
        <PageContentBlock title={`Console — ${container.name}`}>
            <PageHeader
                title={'Console'}
                description={'Interactive console session provided directly by the container host.'}
                actions={
                    <>
                        <Badge
                            variant={state === 'open' ? 'success' : state === 'connecting' ? 'warning' : 'danger'}
                        >
                            {state}
                        </Badge>
                        <Button size={'small'} isSecondary onClick={() => setAttempt((a) => a + 1)}>
                            Reconnect
                        </Button>
                    </>
                }
            />
            {error ? (
                <ErrorState message={error} onRetry={() => setAttempt((a) => a + 1)} />
            ) : !details ? (
                <Spinner centered size={'large'} />
            ) : isWebsocket ? (
                <Card $padded={false}>
                    <div ref={ref} css={tw`w-full h-[520px] p-2`} style={{ backgroundColor: '#0b0e14' }} />
                </Card>
            ) : (
                <Card>
                    <p css={tw`text-sm mb-4`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                        This node&apos;s driver returned a ticket-based console session rather than a websocket
                        endpoint. Use the details below with the host&apos;s console client.
                    </p>
                    <pre
                        css={tw`text-xs rounded-xl p-4 overflow-auto`}
                        style={{ backgroundColor: 'rgb(var(--hyper-surface-3))', color: 'rgb(var(--hyper-text))' }}
                    >
                        {JSON.stringify(details, null, 2)}
                    </pre>
                </Card>
            )}
        </PageContentBlock>
    );
};
