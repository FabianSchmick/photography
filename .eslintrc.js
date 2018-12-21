module.exports = {
    "env": {
        "browser": true,
        "jquery": true
    },
    "extends": "eslint:recommended",
    "parserOptions": {
        "ecmaVersion": 5
    },
    "rules": {
        "arrow-spacing": ["error", { "before": true, "after": true }],
        "block-spacing": ["error", "always"],
        "brace-style": ["error", "1tbs", { "allowSingleLine": true }],
        "comma-dangle": ["error", {
            "arrays": "never",
            "objects": "never",
            "imports": "never",
            "exports": "never",
            "functions": "never"
        }],
        "comma-spacing": ["error", { "before": false, "after": true }],
        "comma-style": ["error", "last"],
        "dot-location": ["error", "property"],
        "eol-last": "error",
        "eqeqeq": ["error", "always", { "null": "ignore" }],
        "func-call-spacing": ["error", "never"],
        "indent": ["error", 4],
        "key-spacing": ["error", { "beforeColon": false, "afterColon": true }],
        "keyword-spacing": ["error", { "before": true, "after": true }],
        "linebreak-style": ["error", "unix"],
        "new-cap": ["error", { "newIsCap": true, "capIsNew": false }],
        "no-mixed-spaces-and-tabs": "error",
        "no-multi-spaces": "error",
        "no-tabs": "error",
        "no-trailing-spaces": "error",
        "no-whitespace-before-property": "error",
        "object-curly-spacing": ["error", "always"],
        "quotes": ["error", "double"],
        "semi": ["error", "always"],
        "semi-spacing": ["error", { "before": false, "after": true }],
        "space-before-blocks": ["error", "always"],
        "space-in-parens": ["error", "never"],
        "yoda": ["error", "never"]
    }
};
