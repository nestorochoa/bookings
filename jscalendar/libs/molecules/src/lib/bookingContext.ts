import { createContext } from 'react';

export const BookingContext = createContext(null);
export const BookingDispatchContext = createContext(null);

export function bookingReducer(
  bookings: any,
  action: { type: any; booking: any }
) {
  switch (action.type) {
    case 'added': {
      return [...bookings, action.booking];
    }
    default: {
      throw Error(`Unknown action ${action.type}`);
    }
  }
}
