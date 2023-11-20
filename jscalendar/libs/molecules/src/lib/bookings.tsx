import { FC, createContext, useReducer } from 'react';
import { Calendar } from 'react-modern-calendar-datepicker';
import styled from 'styled-components';
import 'react-modern-calendar-datepicker/lib/DatePicker.css';

import { Wishlist } from './Wishlist';
import { Toolbar } from './Toolbar';
import { Board } from './Board';
import { DndProvider } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import { User } from './general';
import { BookingProvider } from './bookingContext';

/* eslint-disable-next-line */

export interface FreeDates {
  bd_date: string | Date;
  available: string | boolean;
}

export interface DaySchedule {
  bd_date: string | Date;
  bd_id: string | number;
}

export interface DataInit {
  day_sel: string;
  freeDates: Array<FreeDates>;
  day_schedule: Array<DaySchedule>;
  instructors: Array<User>;
}

export interface BookingProps {
  date: any;
  wishlist: Array<any>;
  setDate: any;
  addInstructor: any;
  dayEvents: Array<any>;
  dateParsed: any;
  freeDatesCalendar: Array<any>;
  day_schedule: Array<any>;
  instructors: Array<any>;
  reloadData: any;
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

export const GlobalBookingContext = createContext<{
  reloadData: any;
  day_schedule: any[];
  dayEvents: any[];
  wishlist: any[];
}>({});

export const BookingsManager: FC<BookingProps> = ({
  date,
  wishlist,
  addInstructor,
  setDate,
  dayEvents,
  dateParsed,
  freeDatesCalendar,
  day_schedule,
  instructors,
  reloadData,
}) => {
  return (
    <StyledContainer>
      <GlobalBookingContext.Provider
        value={{ reloadData, day_schedule, dayEvents, wishlist }}
      >
        <DndProvider backend={HTML5Backend}>
          <BookingProvider>
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
                value={dateParsed}
                onChange={setDate}
                customDaysClassName={freeDatesCalendar}
                calendarClassName="responsive-calendar"
              />
              <Wishlist wishlistEvents={wishlist}></Wishlist>
            </div>
          </BookingProvider>
        </DndProvider>
      </GlobalBookingContext.Provider>
    </StyledContainer>
  );
};

export default BookingsManager;
