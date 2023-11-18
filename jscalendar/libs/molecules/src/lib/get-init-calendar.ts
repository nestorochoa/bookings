import axios from 'axios';
import { useEffect, useState } from 'react';

export interface fetcherProps {
  input: RequestInfo | URL;
  init?: RequestInit | undefined;
}

// eslint-disable-next-line @typescript-eslint/no-empty-interface
export interface UseGetInit {
  data: any;
  error: any;
  isLoading: boolean;
}

export function useGetInit(apiUrl: string): UseGetInit {
  const [data, setData] = useState({});
  const [isLoading, setLoading] = useState(true);
  const [error, setError] = useState<any>(null);

  useEffect(() => {
    (async () => {
      try {
        const response = await axios.get(`${apiUrl}/bookings/init`);
        console.log(response.data);
        const {
          day_sel,
          freeDates,
          instructors,
          select_booking_dates,
          select_students_level,
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
        };

        setData(mapped);

        setLoading(false);
      } catch (e) {
        setError(e);
        setLoading(false);
      }
    })();
  }, [apiUrl]);
  return { data, error, isLoading };
}

export default useGetInit;
