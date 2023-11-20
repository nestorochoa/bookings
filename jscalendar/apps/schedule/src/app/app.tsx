import styled from 'styled-components';
import { BookingsManager, useGetInit } from '@ochoa/molecules';
import { environment } from '../environments/environment';

const StyledApp = styled.div`
  // Your style here
`;

export function App() {
  const {
    data,
    error,
    isLoading,
    wishlist,
    setDate,
    addInstructor,
    dayEvents,
  } = useGetInit(environment.apiUrl);

  console.log(data, error, isLoading);
  return (
    <StyledApp>
      {!isLoading && (
        <BookingsManager
          data={data}
          dayEvents={dayEvents}
          {...{ wishlist, setDate, addInstructor }}
        ></BookingsManager>
      )}
    </StyledApp>
  );
}

export default App;
