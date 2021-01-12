module.exports = {
  "extends": "eslint:recommended",
  "overrides": [{
    "files": ["checkin.js", "manage.js", "modules/*.js"],
    "parserOptions": {
      "sourceType": "module"
    }
  }]
}
