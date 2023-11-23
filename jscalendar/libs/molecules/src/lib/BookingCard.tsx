import {
  faArrowRotateLeft,
  faCircleCheck,
  faCircleXmark,
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { FC, forwardRef } from 'react';
import styled from 'styled-components';
import { generalSettings } from './general';
import { Event } from './types';

const round = (n: number, p = 2) =>
  ((e) => Math.round(n * e) / e)(Math.pow(10, p));

export type StatusOps = 'wishlist' | 'board' | 'drag';

export interface BookCardProps {
  event: Event;
  status: StatusOps;
  dragAttributes?: any;
  dragListeners?: any;
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

  ${({ status, topPosition }: { status: StatusOps; topPosition: string }) => {
    let out = '';
    if (status === 'wishlist') {
      out = `
      .bookBody{
        background-color:#961D26;
      }
    `;
    }
    if (status === 'board') {
      out = `
      position:absolute;
      top:${topPosition};
      .bookBody{
      background-color:#6af;
      }
    `;
    }
    if (status === 'drag') {
      out = `  .bookBody {
      background-color:#66aaff9e;
      }
      `;
    }
    return out;
  }}
`;
export const BookingCard = forwardRef(
  (
    props: BookCardProps,
    outRef:
      | ((instance: HTMLInputElement | null) => void)
      | React.MutableRefObject<HTMLInputElement | null>
      | null
  ) => {
    const { event, status, dragAttributes, dragListeners } = props;
    const {
      start_time,
      level,
      time_description,
      duration,
      student_name,
      student_mobile,
      special,
      obs,
      id,
      count_cancel,
    } = event;
    const topPosition = generalSettings.getPositionInRem(start_time);

    return (
      <StyledCard ref={outRef as any} {...{ status, topPosition }}>
        <div className="Bookheader" {...dragAttributes} {...dragListeners}>
          <div className="info">
            <div className="level">{level}</div>
            <div className="time">{time_description}</div>
            <div className="hours_left">{round(duration / 60)}</div>
          </div>
          {status !== 'drag' && (
            <div className="actions">
              {status !== 'wishlist' && (
                <button>
                  <FontAwesomeIcon icon={faCircleCheck}></FontAwesomeIcon>
                </button>
              )}
              <input type="checkbox" className="check_sms" value={id} />
              <button>
                <FontAwesomeIcon icon={faCircleXmark}></FontAwesomeIcon>
              </button>
              {status !== 'wishlist' && (
                <button>
                  <FontAwesomeIcon icon={faArrowRotateLeft}></FontAwesomeIcon>
                </button>
              )}
              <div className="cancel_times">{count_cancel}</div>
            </div>
          )}
        </div>
        <div className="bookBody">
          {special && special.length > 0 ? (
            <>
              {special.map((elm: any, index) => (
                <div key={`special-${id}-${index}`}>
                  {elm.name} - {elm.phone}
                </div>
              ))}
            </>
          ) : (
            <>
              <div className="info_student">{student_name}</div>
              <div className="info_mobile">{student_mobile}</div>
              <div className="info_notes">{obs}</div>
            </>
          )}
        </div>
      </StyledCard>
    );
  }
);
