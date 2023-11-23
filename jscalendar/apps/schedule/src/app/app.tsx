import styled from 'styled-components';
import { BookingsManager, useGetInit } from '@ochoa/molecules';
import { environment } from '../environments/environment';

const StyledApp = styled.div`
  // Your style here
`;

export function App() {
  const {
    isLoading,
    functions,
    innerLoading,
    dateString,
    dayValues,
    constantValues,
    dateParsed,
  } = useGetInit(environment.apiUrl);

  return (
    <StyledApp>
      {!isLoading && (
        <BookingsManager
          functions={functions}
          innerLoading={innerLoading}
          dateString={dateString}
          dayValues={dayValues}
          constantValues={constantValues}
          dateParsed={dateParsed}
        ></BookingsManager>
      )}
    </StyledApp>
  );
}

export default App;
