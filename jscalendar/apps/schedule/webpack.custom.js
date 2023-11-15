module.exports = (config, context) => {
  const temp = {
    entry: { test: ['/standalone.tsx'], ...config.entry },
    ...config,
  };
  console.log(temp.entry);
  return temp;
};
