module.exports = {
    "env": {
        "browser": true,
        "es6": true,
        "jquery": true
    },
    "extends": "eslint:recommended",
    "globals": {
        "Atomics": "readonly",
        "SharedArrayBuffer": "readonly"
    },
    "parserOptions": {
        "ecmaVersion": 2018,
        "sourceType": "script"
    },
    "rules": {
      "curly": [
        2,
        "all"
      ],
      "operator-linebreak": [
        2,
        "after"
      ],
      "camelcase": [
        2,
        {
          "properties": "never"
        }
      ],
      "max-len": [
        2,
        150
      ],
      "indent": [
        2,
        2,
        {
          "SwitchCase": 1
        }
      ],
      "quotes": [
        2,
        "single"
      ],
      "no-multi-str": 2,
        "no-mixed-spaces-and-tabs": 2,
      "no-trailing-spaces": 2,
      "space-unary-ops": [
        2,
       {
          "nonwords": false,
          "overrides": {}
        }
        ],
      "one-var": [
        2,
        "never"
      ],
     "keyword-spacing": [
        2,
        {}
      ],
      "space-infix-ops": 2,
        "space-before-blocks": [
        2,
        "always"
      ],
     "eol-last": 2,
      "space-before-function-paren": [
        2,
        "never"
      ],
      "array-bracket-spacing": [
        2,
        "never",
        {
          "singleValue": true
        }
      ],
      "space-in-parens": [
        2,
        "never"
      ],
      "no-multiple-empty-lines": 2
    },
    overrides: [
    {
      files: [ "sitesupport/vue/*.js",
               "console/vue.js",
               "modules/concom/report/sitesupport/vue.js",
               "modules/concom/sitesupport/vue.js",
               "modules/event/report/sitesupport/vue.js",
               "modules/registration/sitesupport/badgeMenuPane.js" ,
               "modules/registration/sitesupport/vue.js",
               "modules/registration/sitesupport/boarding.js",
               "modules/registration/sitesupport/checkin.js",
               "modules/registration/sitesupport/lost.js",
               "modules/registration/report/sitesupport/vue.js",
               "modules/volunteers/sitesupport/volunteerPane.js",
               "modules/volunteers/admin/sitesupport/vue.js",
               "modules/volunteers/sitesupport/volunteersEntry.js",
               "modules/volunteers/sitesupport/hourTable.js",
               "modules/volunteers/report/sitesupport/hourReport.js",
               "modules/volunteers/report/sitesupport/volunteerReport.js"],
      parserOptions: { sourceType: "module" },
    }
  ]

};
