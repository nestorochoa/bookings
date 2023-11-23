import { FC, ReactNode, useContext } from 'react';
import styled from 'styled-components';
import { ItemTypes, generalSettings } from './general';
import { useDrop } from 'react-dnd';
import { GlobalBookingContext } from './bookings';
import { useDroppable } from '@dnd-kit/core';

export interface GridProps {
  id?: string;
  date?: string;
}

const StyledSpace = styled.div`
  width: ${generalSettings.getWidth};
  height: ${generalSettings.getHeight};
  border: 1px solid black;
  box-sizing: border-box;
  ${({ isOver }: { isOver?: boolean }) =>
    isOver &&
    `
    background-color:#feffd4;
  `}
`;
const StyledSpaceDescription = styled.div`
  height: ${generalSettings.getHeightDesc};
  border: 1px solid black;
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  align-items: center;
`;
const hours = [
  { military: '08:00:00', ampm: '8am' },
  { military: '09:00:00', ampm: '9am' },
  { military: '10:00:00', ampm: '10am' },
  { military: '11:00:00', ampm: '11am' },
  { military: '12:00:00', ampm: '12pm' },
  { military: '13:00:00', ampm: '1pm' },
  { military: '14:00:00', ampm: '2pm' },
  { military: '15:00:00', ampm: '3pm' },
  { military: '16:00:00', ampm: '4pm' },
  { military: '17:00:00', ampm: '5pm' },
  { military: '18:00:00', ampm: '6pm' },
  { military: '19:00:00', ampm: '7pm' },
  { military: '20:00:00', ampm: '8pm' },
  { military: '21:00:00', ampm: '9pm' },
  { military: '22:00:00', ampm: '10pm' },
];

export interface BoardSpaceProps {
  children?: ReactNode;
  minutes: number;
  group?: string;
}

export const BoardSpace: FC<BoardSpaceProps> = ({ minutes, group }) => {
  return <StyledSpace></StyledSpace>;
};

const DayContainer = styled.div`
  ${({ isOver }: { isOver?: boolean }) => isOver && `background-color:#fcc;`}
`;

export const GridSpace: FC<GridProps> = ({ date, id }) => {
  const { isOver, setNodeRef } = useDroppable({
    id: `droppable-${id}`,
    data: {
      date,
      id,
    },
  });

  return (
    <DayContainer ref={setNodeRef} isOver={isOver}>
      {hours.map(({ ampm, military }, index) =>
        [...Array(4).keys()].map((elm) => {
          const base = index * 60;
          const minutes = base + 15 * elm;

          return (
            <BoardSpace
              key={`desc-1-${elm}`}
              minutes={minutes}
              group={id}
            ></BoardSpace>
          );
        })
      )}
    </DayContainer>
  );
};

export const GridSpaceDesc: FC = () => {
  return (
    <div>
      {hours.map(({ ampm }, index) => (
        <StyledSpaceDescription key={`desc-rr-${index}`}>
          {ampm}
        </StyledSpaceDescription>
      ))}
    </div>
  );
};
