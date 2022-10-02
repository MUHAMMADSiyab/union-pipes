<template>
  <div>
    <Navbar v-if="!printMode" />

    <v-container class="mt-4">
      <v-row>
        <v-col cols="12">
          <v-card :loading="formLoading" :disabled="formLoading">
            <v-card-title primary-title>New Tank</v-card-title>
            <v-card-subtitle>Add a New Tank</v-card-subtitle>

            <v-card-text class="mt-1">
              <v-form @submit.prevent="add">
                <v-row>
                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('name')"
                    ></small>
                    <v-text-field
                      v-model="data.name"
                      label="Name"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('product_id')"
                    ></small>
                    <v-select
                      :items="products"
                      label="Type"
                      item-text="name"
                      item-value="id"
                      v-model="data.product_id"
                      dense
                      outlined
                    ></v-select>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('code')"
                    ></small>
                    <v-text-field
                      v-model="data.code"
                      label="Code"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('current_fuel_quantity')"
                    ></small>
                    <v-text-field
                      v-model="data.current_fuel_quantity"
                      type="number"
                      label="Current Fuel Quantity (in litres)"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('limit')"
                    ></small>
                    <v-text-field
                      v-model="data.limit"
                      type="number"
                      label="Limit/Capacity (in litres)"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col xl="4" lg="4" md="4" sm="12" cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('lower_limit')"
                    ></small>
                    <v-text-field
                      v-model="data.lower_limit"
                      type="number"
                      label="Lower Limit (in litres)"
                      dense
                      outlined
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" class="py-0">
                    <small
                      class="red--text"
                      v-if="validation.hasErrors()"
                      v-text="validation.getMessage('description')"
                    ></small>
                    <v-textarea
                      rows="3"
                      name="description"
                      label="Description"
                      id="description"
                      v-model="data.description"
                      dense
                      outlined
                    ></v-textarea>
                  </v-col>
                </v-row>

                <v-btn color="primary" type="submit">Add</v-btn>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";
import Navbar from "../navs/Navbar";

export default {
  mixins: [ValidationMixin],

  components: {
    Navbar,
  },

  data() {
    return {
      formLoading: false,
      data: {
        name: "",
        product_id: "",
        limit: "",
        current_fuel_quantity: "",
        code: "",
        lower_limit: "",
        description: "",
      },
    };
  },

  methods: {
    ...mapActions({
      getProducts: "product/getProducts",
      addTank: "tank/addTank",
    }),

    async add() {
      this.formLoading = true;

      await this.addTank(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        this.data.name = "";
        this.data.product_id = "";
        this.data.limit = "";
        this.data.code = "";
        this.data.lower_limit = "";
        (this.data.current_fuel_quantity = ""), (this.data.description = "");
        // Clear the validation messages object
        this.validation.setMessages({});
      }
    },
  },

  computed: {
    ...mapGetters({
      products: "product/products",
      validationErrors: "validationErrors",
    }),
  },

  mounted() {
    this.getProducts();
  },
};
</script>