module.exports = {
  "extends": "eslint:recommended",
  "overrides": [{
    "files": ["*/vue/*",
              "vue/*",
              "sitesupport/modules/*"],
    "parserOptions": {
      "sourceType": "module"
    }
  }]
}
