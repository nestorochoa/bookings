import { FC, createContext, useState } from 'react';
import { Calendar } from 'react-modern-calendar-datepicker';
import styled from 'styled-components';
import 'react-modern-calendar-datepicker/lib/DatePicker.css';

import { Wishlist } from './Wishlist';
import { Toolbar } from './Toolbar';
import { Board } from './Board';
import {
  DndContext,
  DragEndEvent,
  DragOverlay,
  DragStartEvent,
  KeyboardSensor,
  MeasuringConfiguration,
  Modifiers,
  MouseSensor,
  SensorDescriptor,
  TouchSensor,
  useSensor,
  useSensors,
} from '@dnd-kit/core';

import { BookingProps, Event } from './types';
import { CircularProgress, Modal } from '@mui/joy';
import { snapToGrid } from './ModifiersDrag';
import { BookingCard } from './BookingCard';

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

export const GlobalBookingContext = createContext<any>({});

export const BookingsManager: FC<BookingProps> = (props) => {
  const [activeItem, setActive] = useState<any>();

  const {
    dayValues: { day_schedule, dayEvents, freeDatesCalendar, wishlist },
    dateParsed,
    constantValues: { instructors },
    functions: { setDate, addInstructor, reloadData, updateEvent },
    innerLoading,
  } = props;

  const mouseSensor = useSensor(MouseSensor);
  const touchSensor = useSensor(TouchSensor);
  const keyboardSensor = useSensor(KeyboardSensor);

  const sensors = useSensors(mouseSensor, touchSensor, keyboardSensor);

  const monitorDrag: {
    // id?: string;
    // accessibility?: {
    //   announcements?: Announcements;
    //   container?: Element;
    //   restoreFocus?: boolean;
    //   screenReaderInstructions?: ScreenReaderInstructions;
    // };
    // autoScroll?: boolean | AutoScrollOptions;
    // cancelDrop?: CancelDrop;
    // children?: React.ReactNode;
    // collisionDetection?: CollisionDetection;
    measuring?: MeasuringConfiguration;
    modifiers?: Modifiers;
    sensors?: SensorDescriptor<any>[];
    onDragStart?(event: DragStartEvent): void;
    // onDragMove?(event: DragMoveEvent): void;
    // onDragOver?(event: DragOverEvent): void;
    onDragEnd?(event: DragEndEvent): void;
    // onDragCancel?(event: DragCancelEvent): void;
  } = {
    onDragStart: (event: DragStartEvent) => {
      if (event.active.data.current)
        setActive(event.active.data.current['event']);
    },
    onDragEnd: (event: any) => {
      console.log(event.activatorEvent.y, event);
      setActive(undefined);
    },
    sensors,
    // modifiers: [snapToGrid],
    // measuring: {
    //   draggable: {
    //     measure: (node) => {
    //       // console.log('the measured node: ', node);
    //       console.log('node measure: ', node.getBoundingClientRect());
    //       return node.getBoundingClientRect();
    //     },
    //   },
    // },
  };
  /**
   * MeasuringConfiguration
 * onDragStart?(event: DragStartEvent): void;
    onDragMove?(event: DragMoveEvent): void;
    onDragOver?(event: DragOverEvent): void;
    onDragEnd?(event: DragEndEvent): void;
    onDragCancel?(event: DragCancelEvent): void;
 */
  return (
    <StyledContainer>
      <GlobalBookingContext.Provider
        value={{ reloadData, day_schedule, dayEvents, wishlist, updateEvent }}
      >
        <Toolbar
          instructors={day_schedule}
          addInstructor={addInstructor}
          className="top"
        ></Toolbar>

        <DndContext {...monitorDrag}>
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

          <DragOverlay>
            {activeItem && (
              <BookingCard event={activeItem} status="drag"></BookingCard>
            )}
          </DragOverlay>
        </DndContext>
        <Modal open={innerLoading}>
          <CircularProgress variant="soft" color="neutral" />
        </Modal>
      </GlobalBookingContext.Provider>
    </StyledContainer>
  );
};

export default BookingsManager;
