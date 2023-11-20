import { FC, ReactNode } from 'react';
import styled from 'styled-components';
import { ItemTypes, generalSettings } from './general';
import { useDrop } from 'react-dnd';

export interface GridProps {
  description: boolean;
  id?: string;
  date?: string;
}

const StyledSpace = styled.div`
  width: ${generalSettings.getWidth};
  height: ${generalSettings.getHeight};
  border: 1px solid black;
  box-sizing: border-box;
  ${({ isOver }: { isOver: boolean }) =>
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
  date: Date;
  id?: string;
}

export const BoardSpace: FC<BoardSpaceProps> = ({ date, id }) => {
  const [{ isOver, canDrop }, drop] = useDrop(() => ({
    accept: ItemTypes.LESSON,
    collect: (monitor) => ({
      isOver: !!monitor.isOver(),
      canDrop: !!monitor.canDrop(),
    }),
    drop: (item, monitor) => {
      console.log(item, monitor, 'On DROP');
      return { date, id };
    },
  }));

  return <StyledSpace ref={drop} {...{ isOver }}></StyledSpace>;
};

export const GridSpace: FC<GridProps> = ({ description, date, id }) => {
  return (
    <div>
      {hours.map(({ ampm, military }, index) =>
        description ? (
          <StyledSpaceDescription key={`desc-rr-${index}`}>
            {ampm}
          </StyledSpaceDescription>
        ) : (
          [...Array(4).keys()].map((elm) => {
            const slotDate = new Date(
              new Date(`${date} ${military}`).getTime() +
                generalSettings.measure15minutes * elm
            );

            return (
              <BoardSpace
                key={`desc-1-${elm}`}
                date={slotDate}
                id={id}
              ></BoardSpace>
            );
          })
        )
      )}
    </div>
  );
};
