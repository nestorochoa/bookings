const general = {
  spaceWidth: 12,
  spaceHeight: 2,
  unit: 'rem',
  initHour: new Date('1980-01-01 8:00:00').getTime(),
  measure15minutes:
    new Date('1980-01-01 8:15:00').getTime() -
    new Date('1980-01-01 8:00:00').getTime(),
};
export const generalSettings = {
  getWidth: `${general.spaceWidth}${general.unit}`,
  getHeight: `${general.spaceHeight}${general.unit}`,
  getHeightDesc: `${general.spaceHeight * 4}${general.unit}`,
  measure15minutes: general.measure15minutes,
  getPositionInRem: (time: string) => {
    const currHour = new Date(`1980-01-01 ${time}`).getTime();
    return (
      (
        ((currHour - general.initHour) * general.spaceHeight) /
        general.measure15minutes
      ).toString() + general.unit
    );
  },
};

export const ItemTypes = {
  LESSON: 'lesson',
};

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
