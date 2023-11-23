import axios from 'axios';
import { useEffect, useState } from 'react';
import {
  DateInterface,
  DaySchedule,
  FreeDates,
  GetBaseInfoBooking,
  GetBaseInfoDayBooking,
  StateHookProps,
  UseGetInit,
} from './types';

export function useGetInit(apiUrl: string): UseGetInit {
  const [data, setData] = useState<StateHookProps>({
    isLoading: true,
    innerLoading: false,
    dateString: '',
    dateParsed: undefined,
    dayValues: {
      day_schedule: [],
      dayEvents: [],
      freeDatesCalendar: [],
      wishlist: [],
    },
    constantValues: {
      instructors: [],
      select_booking_types: [],
      select_student_level: [],
    },
    error: undefined,
  });

  useEffect(() => {
    (async () => {
      const { instructors, select_booking_types, select_student_level } =
        await getBaseInfoBooking(apiUrl);
      setData((preData) => ({
        ...preData,
        constantValues: {
          instructors,
          select_booking_types,
          select_student_level,
        },
      }));
    })();
  }, [apiUrl]);

  const reloadAll = async () => {
    try {
      const { day_sel, freeDates, day_schedule } = await getBaseInfoDayBooking(
        apiUrl,
        data.dateString
      );

      const splitDay = day_sel.split('/');
      const mapped = {
        dateParsed: {
          year: parseInt(splitDay[2]),
          month: parseInt(splitDay[1]),
          day: parseInt(splitDay[0]),
        },
        freeDatesCalendar: freeDates.reduce<Array<any>>(
          (prev, { bd_date, available }: FreeDates) => {
            const splitDate = bd_date.split('-');
            prev.push({
              year: parseInt(splitDate[0]),
              month: parseInt(splitDate[1]),
              day: parseInt(splitDate[2]),
              className:
                available === '1'
                  ? 'highlight dateAvailable'
                  : 'highlight noAvailable',
            });
            return prev;
          },
          []
        ),
      };

      const wishlist = await getWishlist(apiUrl, day_sel);
      const dayEvents = await getDayEvents(apiUrl, day_schedule);

      setData((preData) => ({
        ...preData,
        dayValues: {
          day_schedule,
          dayEvents,
          freeDatesCalendar: mapped.freeDatesCalendar,
          wishlist,
        },
        isLoading: false,
        innerLoading: false,
        dateString: day_sel,
        dateParsed: mapped.dateParsed,
      }));
    } catch (e) {
      setData({
        ...data,
        error: e,
        innerLoading: false,
        isLoading: false,
      });
    }
  };

  useEffect(() => {
    (async () => {
      await reloadAll();
    })();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [apiUrl, data.dateString]);

  const setDate = (date: DateInterface) => {
    setData({
      ...data,
      dateString: `${date.day}/${date.month}/${date.year}`,
      dateParsed: date,
      innerLoading: true,
    });
  };

  const addInstructor = async () => {
    setData((preData) => ({
      ...preData,
      isLoading: true,
    }));
    const addInstructorData = new FormData();
    addInstructorData.append('add_ins', '1');
    addInstructorData.append('date_sel', data.dateString);
    const responseAdd = await axios.post(
      `${apiUrl}/bookings/init`,
      addInstructorData
    );
    // await reloadAll();
    return responseAdd.data;
  };

  const reloadData = async () => {
    const wishlistR = await getWishlist(apiUrl, data.dateString);
    const dayEventsR = await getDayEvents(apiUrl, data.dayValues.day_schedule);
    setData((oldData) => ({
      ...oldData,
      dayValues: {
        ...oldData.dayValues,
        wishlist: wishlistR,
        dayEvents: dayEventsR,
      },
    }));
  };

  const reloadDays = async () => {
    const { day_schedule } = await getBaseInfoDayBooking(
      apiUrl,
      data.dateString
    );
    setData((oldData) => ({
      ...oldData,
      dayValues: {
        ...oldData.dayValues,
        day_schedule,
      },
    }));
  };

  const updateEvent = async (props: {
    type: number;
    group: number;
    minutes: number;
    duration: number;
    student: string;
    id: number;
  }) => {
    const updateData = new FormData();
    Object.entries(props).forEach(([key, value]) => {
      updateData.append(key, value.toString());
    });

    const responseUpdate = await axios.post(
      `${apiUrl}/bookings/json_save_lesson`,
      updateData
    );

    return responseUpdate.data;
  };

  const setInnerLoading = (inner: boolean) => {
    setData((prevData) => ({
      ...prevData,
      innerLoading: inner,
    }));
  };

  return {
    ...data,
    functions: {
      setDate,
      addInstructor,
      reloadData,
      updateEvent,
      reloadDays,
      setInnerLoading,
    },
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
  day_schedule: Array<DaySchedule>
) => {
  const bookIds = day_schedule
    .reduce((prev: Array<string>, { bd_id, bd_inactive }: DaySchedule) => {
      if (!(bd_inactive !== null && bd_inactive === '1')) {
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

export const updateEvent = async (
  apiUrl: string,
  props: {
    type: number;
    group: number;
    minutes: number;
    duration: number;
    student: string;
    id: number;
  }
) => {
  const updateData = new FormData();
  Object.entries(props).forEach(([key, value]) => {
    updateData.append(key, value.toString());
  });

  const responseUpdate = await axios.post(
    `${apiUrl}/bookings/json_save_lesson`,
    updateData
  );

  return responseUpdate.data;
};

export default useGetInit;
