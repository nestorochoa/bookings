import { ReactNode, createContext, useContext, useReducer } from 'react';

export const BookingContext = createContext<State | null>(null);
export const BookingDispatchContext = createContext<any>(null);

export function BookingProvider({ children }: { children: ReactNode }) {
  const [bookings, dispatch] = useReducer(bookingsReducer, {
    dayEvents: [],
    wishlist: [],
    isLoading: false,
  });

  return (
    <BookingContext.Provider value={bookings}>
      <BookingDispatchContext.Provider value={dispatch}>
        {children}
      </BookingDispatchContext.Provider>
    </BookingContext.Provider>
  );
}

export function useTasks() {
  return useContext(BookingContext);
}

export function useTasksDispatch() {
  return useContext(BookingDispatchContext);
}

type State = {
  dayEvents: Array<any>;
  wishlist: Array<any>;
  isLoading: boolean;
  error?: string;
};

type Action = { type: 'new' } | { type: 'update' } | { type: 'dropped' };

function bookingsReducer(state: State, action: Action): State {
  switch (action.type) {
    // case 'added': {
    //   return [...tasks, {
    //     id: action.id,
    //     text: action.text,
    //     done: false
    //   }];
    // }

    default: {
      throw Error('Unknown action: ' + action.type);
    }
  }
}
