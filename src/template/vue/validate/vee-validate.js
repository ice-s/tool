import { extend, localize } from "vee-validate";
import { between, confirmed, digits, dimensions, email, ext, image, oneOf, integer, length, is_not, is, max, max_value, mimes, min, min_value, excluded, numeric, regex, required, required_if, size } from "vee-validate/dist/rules";
import en from "vee-validate/dist/locale/en.json";
import ja from "vee-validate/dist/locale/ja.json";

// Install rules
extend("required", required);
extend("min", min);
extend("max", max);
extend("email", email);
extend("digits", digits);
extend("integer", integer);
extend("between", between);
extend("length", length);
extend("confirmed", confirmed);
extend("min_value", min_value);
extend("max_value", max_value);
extend("numeric", numeric);
extend("regex", regex);

// Install messages
localize({
    ja,
    en
});

localize('ja');
