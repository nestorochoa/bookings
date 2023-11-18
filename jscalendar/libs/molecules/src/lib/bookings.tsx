import { FC, useState } from 'react';
import { Calendar } from 'react-modern-calendar-datepicker';
import styled from 'styled-components';
import 'react-modern-calendar-datepicker/lib/DatePicker.css';
import { parse } from 'path';
/* eslint-disable-next-line */
export interface BookingProps {
  config: Record<string, any>;
  data: any;
}

const StyledContainer = styled.div`
  .highlight {
    background-color: #5cebf2;
    &.noAvailable {
      color: grey;
    }
  }
`;

export const BookingsManager: FC<BookingProps> = ({ config, data }) => {
  const { day_sel, freeDates } = data;
  const [selectedDay, setSelectedDay] = useState<any>(day_sel);

  return (
    <StyledContainer>
      <Calendar
        shouldHighlightWeekends
        value={selectedDay}
        onChange={setSelectedDay}
        customDaysClassName={freeDates}
      />
    </StyledContainer>
  );
};

export default BookingsManager;
