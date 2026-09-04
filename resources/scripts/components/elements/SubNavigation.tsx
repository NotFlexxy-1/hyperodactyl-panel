import styled from 'styled-components/macro';
import tw from 'twin.macro';

const SubNavigation = styled.div`
    ${tw`w-full overflow-x-auto sticky top-14 z-30`};
    background-color: rgb(var(--hyper-surface-1) / 0.85);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgb(var(--hyper-border));

    & > div {
        ${tw`flex items-center text-sm mx-auto px-2`};
        max-width: 1200px;

        & > a,
        & > div {
            ${tw`inline-block py-3 px-4 no-underline whitespace-nowrap transition-all duration-150 font-medium rounded-t-lg`};
            color: rgb(var(--hyper-text-muted));

            &:not(:first-of-type) {
                ${tw`ml-1`};
            }

            &:hover {
                color: rgb(var(--hyper-text));
                background-color: rgb(var(--hyper-brand) / 0.08);
            }

            &:active,
            &.active {
                color: rgb(var(--hyper-text));
                box-shadow: inset 0 -2px rgb(var(--hyper-brand));
            }
        }
    }
`;

export default SubNavigation;
