import React from 'react';
import tw from 'twin.macro';
import { Form, Formik } from 'formik';
import { number, object } from 'yup';
import PageContentBlock from '@/components/elements/PageContentBlock';
import PageHeader from '@/components/elements/PageHeader';
import Card, { CardTitle } from '@/components/elements/Card';
import Button from '@/components/elements/Button';
import Field from '@/components/elements/Field';
import CopyOnClick from '@/components/elements/CopyOnClick';
import FlashMessageRender from '@/components/FlashMessageRender';
import { useFlashKey } from '@/plugins/useFlash';
import { useLxcContainer } from '@/components/lxc/LxcContainerContext';
import updateLxcResources from '@/api/lxc/updateLxcResources';

export default () => {
    const { container, setContainer } = useLxcContainer();
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlashKey('lxc:settings');

    return (
        <PageContentBlock title={`Settings — ${container.name}`}>
            <FlashMessageRender byKey={'lxc:settings'} css={tw`mb-4`} />
            <PageHeader
                title={'Settings'}
                description={'Adjust the resource limits applied to this container on its host.'}
            />
            <div css={tw`grid gap-4 lg:grid-cols-2`}>
                <Card>
                    <CardTitle>Resource limits</CardTitle>
                    <Formik
                        initialValues={{
                            memory: container.limits.memory,
                            swap: container.limits.swap,
                            disk: container.limits.disk,
                            cpu_limit: container.limits.cpu,
                            io_weight: container.limits.io,
                        }}
                        validationSchema={object().shape({
                            memory: number().min(16).required(),
                            swap: number().min(0).required(),
                            disk: number().min(128).required(),
                            cpu_limit: number().min(0).required(),
                            io_weight: number().min(10).max(1000).required(),
                        })}
                        onSubmit={(values, { setSubmitting }) => {
                            clearFlashes();
                            updateLxcResources(container.uuid, {
                                memory: Number(values.memory),
                                swap: Number(values.swap),
                                disk: Number(values.disk),
                                cpu_limit: Number(values.cpu_limit),
                                io_weight: Number(values.io_weight),
                            })
                                .then((updated) => {
                                    setContainer(updated);
                                    addFlash({ type: 'success', message: 'Container limits updated.' });
                                })
                                .catch((error) => clearAndAddHttpError(error))
                                .then(() => setSubmitting(false));
                        }}
                    >
                        {({ isSubmitting }) => (
                            <Form>
                                <div css={tw`grid gap-4 sm:grid-cols-2`}>
                                    <Field name={'memory'} type={'number'} label={'Memory (MiB)'} />
                                    <Field name={'swap'} type={'number'} label={'Swap (MiB)'} />
                                    <Field name={'disk'} type={'number'} label={'Disk (MiB)'} />
                                    <Field
                                        name={'cpu_limit'}
                                        type={'number'}
                                        label={'CPU limit (%)'}
                                        description={'Use 0 for unlimited.'}
                                    />
                                    <Field name={'io_weight'} type={'number'} label={'IO weight (10-1000)'} />
                                </div>
                                <Button css={tw`mt-5`} type={'submit'} disabled={isSubmitting}>
                                    Save changes
                                </Button>
                            </Form>
                        )}
                    </Formik>
                </Card>
                <Card>
                    <CardTitle>Container information</CardTitle>
                    <div css={tw`space-y-3 text-sm`} style={{ color: 'rgb(var(--hyper-text-muted))' }}>
                        <div>
                            <p css={tw`text-2xs uppercase tracking-wide`}>UUID</p>
                            <CopyOnClick text={container.uuid}>
                                <p css={tw`cursor-pointer break-all`} style={{ color: 'rgb(var(--hyper-text))' }}>
                                    {container.uuid}
                                </p>
                            </CopyOnClick>
                        </div>
                        <div>
                            <p css={tw`text-2xs uppercase tracking-wide`}>Image</p>
                            <p style={{ color: 'rgb(var(--hyper-text))' }}>{container.image}</p>
                        </div>
                        <div>
                            <p css={tw`text-2xs uppercase tracking-wide`}>Node</p>
                            <p style={{ color: 'rgb(var(--hyper-text))' }}>
                                {container.node.name} ({container.node.driver})
                            </p>
                        </div>
                        <p css={tw`text-xs`}>
                            Deleting or reinstalling a container must be performed by an administrator from the admin
                            area.
                        </p>
                    </div>
                </Card>
            </div>
        </PageContentBlock>
    );
};
