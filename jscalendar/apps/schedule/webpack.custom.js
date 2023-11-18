module.exports = (config, context) => {
  console.log(context);
  const temp = {
    ...config,
    entry: {
      ...{
        test: [`${context.context.root}/apps/schedule/src/sche/booking.tsx`],
      },
      ...config.entry,
    },
  };
  return temp;
};
