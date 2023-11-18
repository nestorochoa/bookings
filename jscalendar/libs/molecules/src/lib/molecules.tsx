import styled from 'styled-components';

/* eslint-disable-next-line */
export interface MoleculesProps {}

const StyledMolecules = styled.div`
  color: pink;
`;

export function Molecules(props: MoleculesProps) {
  return (
    <StyledMolecules>
      <h1>Welcome to Molecules!</h1>
    </StyledMolecules>
  );
}

export default Molecules;
