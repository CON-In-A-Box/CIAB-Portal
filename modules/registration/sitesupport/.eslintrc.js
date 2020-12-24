module.exports = {
  "extends": "eslint:recommended",
  "overrides": [{
    "files": ["checkin.js", "modules/*.js"],
    "parserOptions": {
      "sourceType": "module"
    }
  }]
}
