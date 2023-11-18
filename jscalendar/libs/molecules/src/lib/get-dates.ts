import { addDays, getWeek, parse } from 'date-fns';
import { useState, useCallback, Dispatch, SetStateAction } from 'react';

// eslint-disable-next-line @typescript-eslint/no-empty-interface
export interface UseGetDates {
  goBack: () => void;
  goAhead: () => void;
  setWeek: Dispatch<SetStateAction<number>>;
  setYear: Dispatch<SetStateAction<number>>;
  years: Array<number>;
  weeks: Array<any>;
  year: number;
  week: number;
}

const years = new Array(6)
  .fill(0)
  .map((elm, index) => new Date().getFullYear() - index);

export function useGetDates(): UseGetDates {
  const [year, setYear] = useState(years[0]);
  const [week, setWeek] = useState(getWeek(new Date()));

  const goBack = () => {
    const newWeek = week - 1;
    console.log('goBack', newWeek);
    if (newWeek < 1) {
      setYear(year - 1);
      setWeek(51);
    } else {
      setWeek(newWeek);
    }
  };

  const goAhead = () => {
    const newWeek = week + 1;
    console.log('goAhead', newWeek);
    if (newWeek > 51) {
      setYear(year + 1);
      setWeek(1);
    } else {
      setWeek(newWeek);
    }
  };

  const weeks = new Array(51).fill(1).map((elm, index) => {
    const ww = index + 1;
    const firstDayWeek = parse(`${ww}`, 'I', new Date(`${year}-12-31`));
    const lastDayWeek = addDays(firstDayWeek, 7);
    return {
      week: ww,
      firstDay: firstDayWeek,
      lastDay: lastDayWeek,
    };
  });
  return { goBack, goAhead, setWeek, setYear, years, weeks, year, week };
}

export default useGetDates;
