import {
  DragCancelEvent,
  DragEndEvent,
  DragMoveEvent,
  DragOverEvent,
  DragStartEvent,
} from '@dnd-kit/core';

export interface Event {
  id: string;
  start_time: string;
  end_time: string;
  time_description: string;
  minutes: string;
  duration: number;
  bk_level: string;
  level: string;
  student: string;
  obs: string;
  hl: string;
  special: Array<any>;
  current: string;
  student_name: string;
  student_mobile: string;
  count_cancel?: string | number;
}

export interface User {
  bk_creation_date: string;
  bk_origin?: string;
  usr_deactive: string | boolean;
  usr_email: string;
  usr_id: string;
  usr_name: string;
  usr_phone_main: string;
  usr_phone_sec: string;
  usr_surname: string;
  usr_type: string;
  usr_username: string;
}

export interface BookingTypes {
  bt_id: string | number;
  bt_description: string;
}

export interface StudentLevel {
  sl_id: string | number;
  sl_description: string;
}

export interface ContextType {
  reloadData: any;
  updateEvent: any;
  day_schedule: any[];
  dayEvents: any[];
  wishlist: any[];
}

export interface FreeDates {
  bd_date: string;
  available: string;
}

export interface DaySchedule {
  bd_date: string;
  bd_id: string;
  bd_inactive?: string;
}

export interface DataInit {
  day_sel: string;
  freeDates: Array<FreeDates>;
  day_schedule: Array<DaySchedule>;
  instructors: Array<User>;
}
export interface DateInterface {
  year: number;
  month: number;
  day: number;
}

export interface CustomDate {
  year: number;
  month: number;
  day: number;
  className: string;
}
export interface DndContextProperties {
  onDragStart?(event: DragStartEvent): void;
  onDragMove?(event: DragMoveEvent): void;
  onDragOver?(event: DragOverEvent): void;
  onDragEnd?(event: DragEndEvent): void;
  onDragCancel?(event: DragCancelEvent): void;
}

export interface fetcherProps {
  input: RequestInfo | URL;
  init?: RequestInit | undefined;
}

export interface ConstantValues {
  select_booking_types: Array<BookingTypes>;
  select_student_level: Array<StudentLevel>;
  instructors: Array<User>;
}

export interface DayValues {
  dayEvents: Array<Event>;
  day_schedule: Array<DaySchedule>;
  freeDatesCalendar: Array<CustomDate>;
  wishlist: Array<Event>;
}

export interface StateHookProps {
  isLoading: boolean;
  innerLoading: boolean;
  dateString: string;
  dateParsed?: DateInterface;
  dayValues: DayValues;
  constantValues: ConstantValues;
  error?: any;
}

export interface FunctionsHook {
  setDate: any;
  addInstructor: any;
  reloadData: any;
  updateEvent: any;
  reloadDays: any;
  setInnerLoading: any;
}

export interface UseGetInit extends StateHookProps {
  functions: FunctionsHook;
}
export interface BookingProps {
  functions: FunctionsHook;
  innerLoading: boolean;
  dateString: string;
  dayValues: DayValues;
  constantValues: ConstantValues;
  dateParsed?: DateInterface;
}

export interface UpdateProps {
  type: number;
  group: number;
  minutes: number;
  duration: number;
  student: string;
  id: number;
}

export interface GetBaseInfoDayBooking {
  (apiUrl: string, date?: string): Promise<{
    day_sel: string;
    freeDates: Array<FreeDates>;
    day_schedule: Array<DaySchedule>;
  }>;
}

export interface GetBaseInfoBooking {
  (apiUrl: string): Promise<{
    instructors: Array<User>;
    select_booking_types: Array<BookingTypes>;
    select_student_level: Array<StudentLevel>;
  }>;
}
