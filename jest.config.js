/* globals module */
const config = {
  coverageDirectory: '/coverage',
  coveragePathIgnorePatterns: [ '/node_modules' ],
  coverageThreshold: {
    global: {
      branches: 95,
      functions: 95,
      lines: 95,
      statements: 95
    }
  },
  testEnvironment: 'node',
  testMatch: [ '**/test/sitesupport/**/*.test.js' ],
  testPathIgnorePatterns: [ '/node_modules/' ],
  verbose: true
};

module.exports = config;
