import styled from 'styled-components';
import { BookingsManager, useGetInit } from '@ochoa/molecules';
import { environment } from '../environments/environment';

const StyledApp = styled.div`
  // Your style here
`;

export function App() {
  const {
    error,
    isLoading,
    setDate,
    wishlist,
    addInstructor,
    dayEvents,
    date,
    dateParsed,
    freeDatesCalendar,
    day_schedule,
    instructors,
    reloadData,
  } = useGetInit(environment.apiUrl);

  return (
    <StyledApp>
      {!isLoading && (
        <BookingsManager
          {...{
            wishlist,
            setDate,
            addInstructor,
            date,
            dayEvents,
            dateParsed,
            freeDatesCalendar,
            day_schedule,
            instructors,
            reloadData,
          }}
        ></BookingsManager>
      )}
    </StyledApp>
  );
}

export default App;
