import axios from 'axios';
import { useEffect, useState } from 'react';
import { FreeDates } from './bookings';
import { BookingTypes, StudentLevel, User } from './general';

export interface fetcherProps {
  input: RequestInfo | URL;
  init?: RequestInit | undefined;
}

export interface StateProps {
  wishlist: Array<any>;
  isLoading: boolean;
  date: string;
  error?: any;
  day_sel: string;
  dayEvents: Array<any>;
  instructors: Array<User>;
  select_booking_types: Array<BookingTypes>;
  select_student_level: Array<StudentLevel>;
  day_schedule: Array<any>;
  freeDatesCalendar: Array<any>;
  dateParsed: any;
}

// eslint-disable-next-line @typescript-eslint/no-empty-interface
export interface UseGetInit {
  error?: any;
  isLoading: boolean;
  setDate: any;
  wishlist: any;
  addInstructor: any;
  dayEvents: any;
  date: string;
  dateParsed: string;
  freeDatesCalendar: Array<any>;
  day_schedule: Array<any>;
  instructors: Array<User>;
  reloadData: any;
}

interface GetBaseInfoDayBooking {
  (apiUrl: string, date?: string): Promise<{
    day_sel: string;
    freeDates: Array<FreeDates>;
    day_schedule: Array<any>;
  }>;
}

interface GetBaseInfoBooking {
  (apiUrl: string): Promise<{
    instructors: Array<User>;
    select_booking_types: Array<BookingTypes>;
    select_student_level: Array<StudentLevel>;
  }>;
}

export function useGetInit(apiUrl: string): UseGetInit {
  const [data, setData] = useState<StateProps>({
    instructors: [],
    select_booking_types: [],
    select_student_level: [],
    wishlist: [],
    dayEvents: [],
    isLoading: true,
    date: '',
    error: null,
    day_sel: '',
    day_schedule: [],
    freeDatesCalendar: [],
    dateParsed: {},
  });

  useEffect(() => {
    (async () => {
      const { instructors, select_booking_types, select_student_level } =
        await getBaseInfoBooking(apiUrl);
      setData((preData) => ({
        ...preData,
        instructors,
        select_booking_types,
        select_student_level,
      }));
    })();
  }, [apiUrl]);

  useEffect(() => {
    (async () => {
      try {
        const { day_sel, freeDates, day_schedule } =
          await getBaseInfoDayBooking(apiUrl, data.date);

        const splitDay = day_sel.split('/');
        const mapped = {
          dateParsed: {
            year: parseInt(splitDay[2]),
            month: parseInt(splitDay[1]),
            day: parseInt(splitDay[0]),
          },
          freeDatesCalendar: freeDates.map(({ bd_date, available }: any) => {
            const splitDate = bd_date.split('-');
            return {
              year: parseInt(splitDate[0]),
              month: parseInt(splitDate[1]),
              day: parseInt(splitDate[2]),
              className:
                available === '1'
                  ? 'highlight dateAvailable'
                  : 'highlight noAvailable',
            };
          }),
          day_schedule,
        };

        const wishlist = await getWishlist(apiUrl, day_sel);
        const dayEvents = await getDayEvents(apiUrl, day_schedule);

        setData((preData) => ({
          ...preData,
          wishlist,
          isLoading: false,
          dayEvents,
          date: day_sel,
          ...mapped,
        }));
      } catch (e) {
        setData({
          ...data,
          error: e,
          isLoading: false,
        });
      }
    })();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [apiUrl, data.date]);

  const setDate = (date: any) => {
    console.log(date, 'Before');
    setData({ ...data, date: `${date.day}/${date.month}/${date.year}` });
  };

  const addInstructor = async () => {
    const addInstructorData = new FormData();
    addInstructorData.append('add_ins', '1');
    addInstructorData.append('date_sel', data.day_sel);
    const responseAdd = await axios.post(
      `${apiUrl}/bookings/wishlist`,
      addInstructorData
    );
    return responseAdd.data;
  };

  const reloadData = async () => {
    const wishlistR = await getWishlist(apiUrl, data.day_sel);
    const dayEventsR = await getDayEvents(apiUrl, data.day_schedule);
    setData((oldData) => ({
      ...oldData,
      wishlist: wishlistR,
      dayEvents: dayEventsR,
    }));
  };

  return {
    ...data,
    setDate,
    addInstructor,
    reloadData,
  };
}

export const getBaseInfoDayBooking: GetBaseInfoDayBooking = async (
  apiUrl,
  date
) => {
  const bookingData = new FormData();

  if (date && date !== '') {
    bookingData.append('date_sel', date);
  }
  const response = await axios.post(`${apiUrl}/bookings/init`, bookingData);
  const { day_sel, freeDates, day_schedule } = response.data;
  return {
    day_sel,
    freeDates,
    day_schedule,
  };
};

export const getBaseInfoBooking: GetBaseInfoBooking = async (apiUrl) => {
  const response = await axios.get(`${apiUrl}/bookings/baseInfo`);
  const { instructors, select_booking_types, select_student_level } =
    response.data;
  return { instructors, select_booking_types, select_student_level };
};

export const getWishlist = async (apiUrl: string, date: string) => {
  const wishlistData = new FormData();
  wishlistData.append('date_a', date);

  const responseWishList = await axios.post(
    `${apiUrl}/bookings/wishlist`,
    wishlistData
  );

  const { events } = responseWishList.data;

  return events;
};

export const getDayEvents = async (
  apiUrl: string,
  day_schedule: Array<any>
) => {
  const bookIds = day_schedule
    .reduce((prev: Array<string>, { bd_id, bd_inactive }: any) => {
      if (!(bd_inactive !== null && bd_inactive.toString() === '1')) {
        prev.push(bd_id);
      }
      return prev;
    }, [])
    .join(',');

  const eventsData = new FormData();
  eventsData.append('id_chain', bookIds);

  const responseEvents =
    bookIds !== ''
      ? await axios.post(`${apiUrl}/bookings/json_event`, eventsData)
      : { data: { events: [] } };

  const { events: dayEvents } = responseEvents.data;

  return dayEvents;
};

export default useGetInit;
