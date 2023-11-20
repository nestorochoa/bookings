import axios from 'axios';
import { group } from 'console';
import { useEffect, useState } from 'react';

export interface fetcherProps {
  input: RequestInfo | URL;
  init?: RequestInit | undefined;
}

export interface StateProps {
  data: any;
  wishlist: Array<any>;
  isLoading: boolean;
  date: string;
  error: any;
  day_sel: string;
  dayEvents: Array<any>;
}

// eslint-disable-next-line @typescript-eslint/no-empty-interface
export interface UseGetInit {
  data: any;
  error: any;
  isLoading: boolean;
  setDate: any;
  wishlist: any;
  addInstructor: any;
  dayEvents: any;
}

export function useGetInit(apiUrl: string): UseGetInit {
  const [data, setData] = useState<StateProps>({
    data: {},
    wishlist: [],
    dayEvents: [],
    isLoading: true,
    date: '',
    error: null,
    day_sel: '',
  });

  useEffect(() => {
    (async () => {
      try {
        const bookingData = new FormData();
        if (data.date !== '') {
          bookingData.append('date_sel', data.date);
        }

        const response = await axios.post(
          `${apiUrl}/bookings/init`,
          bookingData
        );
        const {
          day_sel,
          freeDates,
          instructors,
          select_booking_types,
          select_student_level,
          day_schedule,
        } = response.data;

        const splitDay = day_sel.split('/');
        const mapped = {
          day_sel: {
            year: parseInt(splitDay[2]),
            month: parseInt(splitDay[1]),
            day: parseInt(splitDay[0]),
          },
          freeDates: JSON.parse(freeDates).map(
            ({ bd_date, available }: any) => {
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
            }
          ),
          instructors,
          select_booking_types,
          select_student_level,
          day_schedule,
        };

        const bookIds = day_schedule
          .reduce((prev: Array<string>, { bd_id, bd_inactive }: any) => {
            if (!(bd_inactive !== null && bd_inactive.toString() === '1')) {
              prev.push(bd_id);
            }
            return prev;
          }, [])
          .join(',');

        const wishlistData = new FormData();
        wishlistData.append('date_a', day_sel);

        const responseWishList = await axios.post(
          `${apiUrl}/bookings/wishlist`,
          wishlistData
        );
        const { events } = responseWishList.data;

        const eventsData = new FormData();
        eventsData.append('id_chain', bookIds);

        const responseEvents =
          bookIds !== ''
            ? await axios.post(`${apiUrl}/bookings/json_event`, eventsData)
            : { data: [] };
        const { events: dayEvents } = responseEvents.data;

        setData({
          ...data,
          data: mapped,
          wishlist: events,
          isLoading: false,
          dayEvents,
          day_sel,
        });
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

  const setDate = (date: string) => {
    setData({ ...data, date });
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

  return {
    ...data,
    setDate,
    addInstructor,
  };
}

export default useGetInit;
