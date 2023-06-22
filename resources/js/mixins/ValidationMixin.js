import Validation from "../utils/Validation";

const ValidationMixin = {
    data() {
        return {
            validation: new Validation()
        };
    }
};

export default ValidationMixin;
