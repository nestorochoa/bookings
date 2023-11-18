import styled from 'styled-components';
import { BookingsManager, useGetInit } from '@ochoa/molecules';
import { environment } from '../environments/environment';

const StyledApp = styled.div`
  // Your style here
`;

export function App() {
  const { data, error, isLoading } = useGetInit(environment.apiUrl);

  console.log(data, error, isLoading);
  return (
    <StyledApp>
      {!isLoading && (
        <BookingsManager config={{}} data={data}></BookingsManager>
      )}
    </StyledApp>
  );
}

export default App;
