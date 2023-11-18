import { Dispatch, FC, SetStateAction, useState } from 'react';
import styled from 'styled-components';
import { format, getWeek, getYear, parse, startOfWeek } from 'date-fns';
import useGetDates from './get-dates';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faBackwardStep,
  faForwardStep,
} from '@fortawesome/free-solid-svg-icons';

/* eslint-disable-next-line */
export interface ScheduleProps {}
export interface GridProps {
  description: boolean;
  id?: string;
}
const StyledSchedule = styled.div`
  display: flex;
`;

const MainContainer = styled.div``;

const genSize = 9;
const genHeightSize = 2;
const StyledSpace = styled.div`
  width: ${genSize}rem;
  height: ${genHeightSize}rem;
  border: 1px solid black;
  box-sizing: border-box;
`;
const StyledSpaceDescription = styled.div`
  width: ${genSize}rem;
  height: ${genHeightSize * 4}rem;
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

const daysWeek = [
  { day: 1, nameDay: 'Monday' },
  { day: 2, nameDay: 'Tuesday' },
  { day: 3, nameDay: 'Wednesday' },
  { day: 4, nameDay: 'Thursday' },
  { day: 5, nameDay: 'Friday' },
  { day: 6, nameDay: 'Saturday' },
  { day: 7, nameDay: 'Sunday' },
];

export interface NavDatesProps {
  goBack: () => void;
  goAhead: () => void;
  setWeek: Dispatch<SetStateAction<number>>;
  setYear: Dispatch<SetStateAction<number>>;
  years: Array<number>;
  weeks: Array<any>;
  year: number;
  week: number;
}

const NavDates: FC<NavDatesProps> = ({
  goBack,
  goAhead,
  setWeek,
  setYear,
  years,
  weeks,
  year,
  week,
}) => {
  return (
    <div>
      <button
        onClick={goBack}
        disabled={year === years[years.length - 1] && week === 1}
      >
        <FontAwesomeIcon icon={faBackwardStep} />
      </button>
      <select
        value={week}
        onChange={(evt) => {
          setWeek(parseInt(evt.target.value));
        }}
      >
        {weeks.map((elm, index) => {
          return (
            <option value={elm.week} key={`year-${index}`}>
              {format(elm.firstDay, `dd-MM`)}
            </option>
          );
        })}
      </select>
      <select
        value={year}
        onChange={(evt) => {
          setYear(parseInt(evt.target.value));
        }}
      >
        {years.map((elm, index) => (
          <option value={elm} key={`ww-${index}`}>
            {elm}
          </option>
        ))}
      </select>

      <button onClick={goAhead} disabled={year === years[0] && week === 51}>
        <FontAwesomeIcon icon={faForwardStep} />
      </button>
    </div>
  );
};

const GridSpace: FC<GridProps> = ({ description, id }) => {
  return (
    <div>
      <div>HEAD</div>
      {hours.map((hour, index) =>
        description ? (
          <StyledSpaceDescription key={`desc-rr-${index}`}>
            {hour.ampm}
          </StyledSpaceDescription>
        ) : (
          <>
            <StyledSpace key={`desc-1-${index}`}></StyledSpace>
            <StyledSpace key={`desc-2-${index}`}></StyledSpace>
            <StyledSpace key={`desc-3-${index}`}></StyledSpace>
            <StyledSpace key={`desc-4-${index}`}></StyledSpace>
          </>
        )
      )}
    </div>
  );
};

export function Schedule(props: ScheduleProps) {
  const { weeks, years, goBack, goAhead, week, year, setWeek, setYear } =
    useGetDates();

  return (
    <MainContainer>
      <NavDates
        weeks={weeks}
        years={years}
        goBack={goBack}
        goAhead={goAhead}
        setWeek={setWeek}
        setYear={setYear}
        year={year}
        week={week}
      ></NavDates>
      <StyledSchedule>
        <GridSpace description={true} key={`grid-base`}></GridSpace>
        {daysWeek.map((day, index) => (
          <GridSpace description={false} key={`grid-${index}`}></GridSpace>
        ))}
      </StyledSchedule>
    </MainContainer>
  );
}

export default Schedule;
