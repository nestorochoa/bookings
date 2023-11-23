import { FC } from 'react';
import styled from 'styled-components';
import { GridSpace, GridSpaceDesc } from './GridSpace';
import { BookingCard } from './BookingCard';
import { DraggableBooking } from './DraggableBooking';

export interface BoardProps {
  instructors: Array<any>;
  days: Array<any>;
  className?: string;
  readonly?: boolean;
  dayEvents: Array<any>;
}

const BoardContainer = styled.div`
  display: flex;
  overflow-x: auto;
  align-items: flex-end;
`;

const ListDraggable = styled.ul`
  position: relative;
  margin: 0;
  padding: 0;
`;

export const Board: FC<BoardProps> = ({
  instructors,
  days,
  className,
  dayEvents,
}) => {
  return (
    <BoardContainer {...{ className }}>
      {days && days.length > 0 && (
        <>
          <GridSpaceDesc></GridSpaceDesc>
          {days.map((day, index) => {
            const { bd_id, bd_date } = day;
            const events = dayEvents.filter((event) => event.group === bd_id);
            return (
              <div>
                <ListDraggable key={`col-${index}`}>
                  {events.map((event: any, eventIndex: any) => (
                    <DraggableBooking
                      event={event}
                      status="board"
                    ></DraggableBooking>
                  ))}
                  <GridSpace id={bd_id} date={bd_date}></GridSpace>
                </ListDraggable>
              </div>
            );
          })}
        </>
      )}
    </BoardContainer>
  );
};
