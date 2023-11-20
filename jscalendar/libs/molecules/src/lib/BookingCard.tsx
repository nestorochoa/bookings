import {
  faArrowRotateLeft,
  faCircleCheck,
  faCircleXmark,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { FC } from 'react';
import styled from 'styled-components';
import { ItemTypes, generalSettings } from './general';
import { useDrag } from 'react-dnd';

const round = (n: number, p = 2) =>
  ((e) => Math.round(n * e) / e)(Math.pow(10, p));

export interface BookCardProps {
  id: string;
  start_time: string;
  end_time: string;
  time_description: string;
  minutes: string;
  duration: number;
  bk_level: string;
  level: string;
  student: string;
  obs: string;
  hl: string;
  special: Array<any>;
  count_cancel: string;
  current: string;
  wishlist: boolean;
  student_name: string;
  student_mobile: string;
}
const StyledCard = styled.div`
  --round-edge: 0.3rem;
  width: ${generalSettings.getWidth};
  min-height: ${generalSettings.getHeightDesc};
  border: 1px solid #ccc;
  border-radius: var(--round-edge);
  font-size: 0.85rem;
  display: flex;
  flex-direction: column;
  .Bookheader {
    border-radius: var(--round-edge) var(--round-edge) 0 0;
    padding: 0.2rem 0.4rem;
    background-color: #02e;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    .info,
    .actions {
      display: flex;
    }
  }
  .info {
    & > div {
      margin: 0px 0.1rem;
      font-weight: 100;
    }
  }
  .actions {
    button {
      background-color: transparent;
      color: #fff;
      border: 0;
    }
  }

  .bookBody {
    border-radius: 0 0 var(--round-edge) var(--round-edge);
    flex-grow: 1;
    color: #fff;
    padding: 0.2rem 0.4rem;
    overflow: auto;
  }
  ${({ wishlist, topPosition }: { wishlist: boolean; topPosition: string }) => {
    return wishlist
      ? `
    .bookBody{
      background-color:#961D26;
    }
  `
      : `
    position:absolute;
    top:${topPosition};
    .bookBody{
    background-color:#6af;
    }
  `;
  }}
`;
export const BookingCard: FC<BookCardProps> = (props) => {
  const {
    id,
    count_cancel,
    duration,
    special,
    student_name,
    student_mobile,
    time_description,
    level,
    wishlist,
    obs,
    start_time,
  } = props;
  const topPosition = generalSettings.getPositionInRem(start_time);

  const [{ isDragging }, drag] = useDrag(() => ({
    type: ItemTypes.LESSON,
    collect: (monitor) => ({
      isDragging: !!monitor.isDragging(),
    }),
  }));

  return (
    <StyledCard {...{ wishlist, topPosition }} ref={drag}>
      <div className="Bookheader">
        <div className="info">
          <div className="level">{level}</div>
          <div className="time">{time_description}</div>
          <div className="hours_left">{round(duration / 60)}</div>
        </div>
        <div className="actions">
          {!wishlist && (
            <button>
              <FontAwesomeIcon icon={faCircleCheck}></FontAwesomeIcon>
            </button>
          )}
          <input type="checkbox" className="check_sms" value={id} />
          <button>
            <FontAwesomeIcon icon={faCircleXmark}></FontAwesomeIcon>
          </button>
          {!wishlist && (
            <button>
              <FontAwesomeIcon icon={faArrowRotateLeft}></FontAwesomeIcon>
            </button>
          )}
          <div className="cancel_times">{count_cancel}</div>
        </div>
      </div>
      <div className="bookBody">
        {special.length === 0 ? (
          <>
            <div className="info_student">{student_name}</div>
            <div className="info_mobile">{student_mobile}</div>
            <div className="info_notes">{obs}</div>
          </>
        ) : (
          <>
            {special.map((elm: any, index) => (
              <div key={`special-${id}-${index}`}>
                {elm.name} - {elm.phone}
              </div>
            ))}
          </>
        )}
      </div>
    </StyledCard>
  );
};
