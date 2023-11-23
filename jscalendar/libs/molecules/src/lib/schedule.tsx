import { Dispatch, FC, SetStateAction, useState } from 'react';
import styled from 'styled-components';
import { format, getWeek, getYear, parse, startOfWeek } from 'date-fns';
import useGetDates from './get-dates';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import {
  faBackwardStep,
  faForwardStep,
} from '@fortawesome/free-solid-svg-icons';
import { GridSpace, GridSpaceDesc } from './GridSpace';

/* eslint-disable-next-line */
export interface ScheduleProps {}

const StyledSchedule = styled.div`
  display: flex;
`;

const MainContainer = styled.div``;

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
        <GridSpaceDesc></GridSpaceDesc>
        {daysWeek.map((day, index) => (
          <GridSpace key={`grid-${index}`}></GridSpace>
        ))}
      </StyledSchedule>
    </MainContainer>
  );
}

export default Schedule;
