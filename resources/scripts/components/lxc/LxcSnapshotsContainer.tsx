import React, { useEffect, useState } from 'react';
import tw from 'twin.macro';
import { Form, Formik } from 'formik';
import { object, string } from 'yup';
import { faCameraRetro } from '@fortawesome/free-solid-svg-icons';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Button from '@/components/elements/Button';
import Field from '@/components/elements/Field';
import Spinner from '@/components/elements/Spinner';
import EmptyState from '@/components/elements/EmptyState';
import FlashMessageRender from '@/components/FlashMessageRender';
import { Dialog } from '@/components/elements/dialog';
import { useFlashKey } from '@/plugins/useFlash';
import { useLxcContainer } from '@/components/lxc/LxcContainerContext';
import { createLxcSnapshot, getLxcSnapshots, restoreLxcSnapshot } from '@/api/lxc/snapshots';
import { LxcSnapshot } from '@/api/lxc/types';

export default () => {
    const { container } = useLxcContainer();
    const { clearAndAddHttpError, clearFlashes, addFlash } = useFlashKey('lxc:snapshots');
    const [snapshots, setSnapshots] = useState<LxcSnapshot[] | null>(null);
    const [restoring, setRestoring] = useState<string | null>(null);
    const [busy, setBusy] = useState(false);

    const load = () => {
        getLxcSnapshots(container.uuid)
            .then(setSnapshots)
            .catch((error) => {
                setSnapshots([]);
                clearAndAddHttpError(error);
            });
    };

    useEffect(load, [container.uuid]);

    const doRestore = (name: string) => {
        setBusy(true);
        clearFlashes();
        restoreLxcSnapshot(container.uuid, name)
            .then(() => addFlash({ type: 'success', message: `Snapshot ${name} restored.` }))
            .catch((error) => clearAndAddHttpError(error))
            .then(() => {
                setBusy(false);
                setRestoring(null);
                load();
            });
    };

    return (
        <PageContentBlock title={`Snapshots — ${container.name}`}>
            <FlashMessageRender byKey={'lxc:snapshots'} css={tw`mb-4`} />
            <PageHeader
                title={'Snapshots'}
                description={'Point-in-time snapshots taken directly on the container host.'}
            />
            <Dialog.Confirm
                open={!!restoring}
                title={'Restore snapshot'}
                confirm={'Restore'}
                onClose={() => setRestoring(null)}
                onConfirmed={() => restoring && doRestore(restoring)}
            >
                Restoring <strong>{restoring}</strong> will discard all changes made to this container since the
                snapshot was taken.
            </Dialog.Confirm>
            <div css={tw`grid gap-4 lg:grid-cols-3`}>
                <Card css={tw`lg:col-span-1`}>
                    <CardTitle>Create snapshot</CardTitle>
                    <Formik
                        initialValues={{ name: '' }}
                        validationSchema={object().shape({
                            name: string().required('A snapshot name is required.').max(60),
                        })}
                        onSubmit={({ name }, { setSubmitting, resetForm }) => {
                            clearFlashes();
                            createLxcSnapshot(container.uuid, name)
                                .then(() => {
                                    addFlash({ type: 'success', message: `Snapshot ${name} created.` });
                                    resetForm();
                                    load();
                                })
                                .catch((error) => clearAndAddHttpError(error))
                                .then(() => setSubmitting(false));
                        }}
                    >
                        {({ isSubmitting }) => (
                            <Form>
                                <Field name={'name'} label={'Snapshot name'} maxLength={60} />
                                <Button css={tw`mt-4 w-full`} type={'submit'} disabled={isSubmitting || busy}>
                                    Create snapshot
                                </Button>
                            </Form>
                        )}
                    </Formik>
                </Card>
                <Card css={tw`lg:col-span-2`}>
                    <CardTitle>Existing snapshots</CardTitle>
                    {!snapshots ? (
                        <Spinner centered />
                    ) : snapshots.length === 0 ? (
                        <EmptyState
                            icon={faCameraRetro}
                            title={'No snapshots'}
                            description={'This container does not have any snapshots on its host yet.'}
                        />
                    ) : (
                        <div css={tw`divide-y`} style={{ borderColor: 'rgb(var(--hyper-border))' }}>
                            {snapshots.map((snapshot) => (
                                <div key={snapshot.name} css={tw`flex items-center justify-between gap-3 py-3`}>
                                    <div css={tw`min-w-0`}>
                                        <p
                                            css={tw`font-medium truncate`}
                                            style={{ color: 'rgb(var(--hyper-text))' }}
                                        >
                                            {snapshot.name}
                                        </p>
                                        <p
                                            css={tw`text-xs mt-1`}
                                            style={{ color: 'rgb(var(--hyper-text-muted))' }}
                                        >
                                            {snapshot.createdAt
                                                ? snapshot.createdAt.toLocaleString()
                                                : 'Creation time unavailable'}
                                        </p>
                                    </div>
                                    <Button
                                        size={'xsmall'}
                                        isSecondary
                                        disabled={busy}
                                        onClick={() => setRestoring(snapshot.name)}
                                    >
                                        Restore
                                    </Button>
                                </div>
                            ))}
                        </div>
                    )}
                </Card>
            </div>
        </PageContentBlock>
    );
};
