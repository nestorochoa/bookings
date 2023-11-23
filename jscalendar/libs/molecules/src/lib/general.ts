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
