import styled from 'styled-components';

/* eslint-disable-next-line */
export interface AtomsProps {}

const StyledAtoms = styled.div`
  color: pink;
`;

export function Atoms(props: AtomsProps) {
  return (
    <StyledAtoms>
      <h1>Welcome to Atoms!</h1>
    </StyledAtoms>
  );
}

export default Atoms;
