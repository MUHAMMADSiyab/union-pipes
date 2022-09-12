<template>
  <v-card :loading="formLoading" :disabled="formLoading">
    <v-card-title primary-title>New Dispenser</v-card-title>
    <v-card-subtitle>Edit Dispenser</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-form @submit.prevent="update">
        <v-row>
          <v-col cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('name')"
            ></small>
            <v-text-field
              name="dispenser-name"
              label="Dispenser Name"
              id="dispenser-name"
              v-model="data.name"
              dense
              outlined
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="py-0">
            <small
              class="red--text"
              v-if="validation.hasErrors()"
              v-text="validation.getMessage('tank_id')"
            ></small>
            <v-select
              :items="tanks"
              item-text="name"
              item-value="id"
              v-model="data.tank_id"
              placeholder="Select Tank"
              autocomplete
              dense
              outlined
            ></v-select>
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

        <v-btn color="success" type="submit">Update</v-btn>
        <v-btn color="secondary" @click="closeDialog">Cancel</v-btn>
      </v-form>
    </v-card-text>
  </v-card>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import ValidationMixin from "../../mixins/ValidationMixin";

export default {
  props: ["singleDispenser"],

  mixins: [ValidationMixin],

  data() {
    const { id, name, tank_id, description } = this.singleDispenser;

    return {
      formLoading: false,
      data: {
        id,
        name,
        tank_id,
        description,
      },
    };
  },

  methods: {
    ...mapActions({
      updateDispenser: "dispenser/updateDispenser",
      getTanks: "tank/getTanks",
    }),

    async update() {
      this.formLoading = true;

      await this.updateDispenser(this.data);

      this.formLoading = false;

      // Validation
      if (this.validationErrors !== null) {
        this.validation.setMessages(this.validationErrors.errors);
      } else {
        // Clear the validation messages object
        this.validation.setMessages({});
        this.closeDialog();
      }
    },

    closeDialog() {
      this.$emit("closeDialog");
    },
  },

  watch: {
    singleDispenser: {
      handler({ id, name }) {
        this.data.id = id;
        this.data.name = name;
        this.data.tank_id = tank_id;
        this.data.description = description;
      },
    },
  },

  computed: {
    ...mapGetters({
      validationErrors: "validationErrors",
      tanks: "tank/tanks",
    }),
  },

  mounted() {
    this.getTanks();
  },
};
</script>