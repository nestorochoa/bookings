import { FC, useReducer } from 'react';
import { Calendar } from 'react-modern-calendar-datepicker';
import styled from 'styled-components';
import 'react-modern-calendar-datepicker/lib/DatePicker.css';

import { Wishlist } from './Wishlist';
import { Toolbar } from './Toolbar';
import { Board } from './Board';
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';

import { bookingReducer } from './bookingContext';
/* eslint-disable-next-line */
export interface BookingProps {
  data: any;
  wishlist: Array<any>;
  setDate: any;
  addInstructor: any;
  dayEvents: Array<any>;
}

const StyledContainer = styled.div`
  @import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');
  * {
    font-family: 'Open Sans', sans-serif;
    div {
      box-sizing: border-box;
    }
  }
  .highlight {
    background-color: #5cebf2;
    &.noAvailable {
      color: grey;
    }
  }
  .responsive-calendar {
    font-family: Arial;
    /* by setting font-size, all the elements will correspond */
    font-size: 9px !important; /* default to 10px */
  }

  // @media (max-width: 1500px) {
  //   .responsive-calendar {
  //     font-size: 8px !important;
  //   }
  // }

  // @media (max-width: 1200px) {
  //   .responsive-calendar {
  //     font-size: 7px !important;
  //   }
  // }

  // @media (max-width: 768px) {
  //   .responsive-calendar {
  //     font-size: 6px !important;
  //   }
  // }

  // /* Large screens */
  // @media (min-width: 2500px) {
  //   .responsive-calendar {
  //     font-size: 12px !important;
  //   }
  // }

  display: grid;
  gap: 1rem 1rem;
  width: 100%;
  grid-template-columns: 80% 300px;
  grid-template-rows: auto auto;

  .top {
    grid-column-start: span 3;
  }
`;

export const BookingsManager: FC<BookingProps> = ({
  data,
  wishlist,
  addInstructor,
  setDate,
  dayEvents,
}) => {
  const { date, freeDates, day_schedule, instructors } = data;
  console.log(date, 'date');
  // eslint-disabl  e-next-line @typescript-eslint/no-unused-vars
  const [bookings, dispatch] = useReducer(bookingReducer, day_schedule);

  return (
    <StyledContainer>
      <DndProvider backend={HTML5Backend}>
        <Toolbar
          instructors={day_schedule}
          addInstructor={addInstructor}
          className="top"
        ></Toolbar>
        <Board
          instructors={instructors}
          days={day_schedule}
          dayEvents={dayEvents}
          className="left"
        ></Board>
        <div className="right">
          <Calendar
            shouldHighlightWeekends
            value={date}
            onChange={setDate}
            customDaysClassName={freeDates}
            calendarClassName="responsive-calendar"
          />
          <Wishlist wishlistEvents={wishlist}></Wishlist>
        </div>
      </DndProvider>
    </StyledContainer>
  );
};

export default BookingsManager;
