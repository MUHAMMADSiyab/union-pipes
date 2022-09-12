<template>
  <v-card :loading="loading" :disabled="loading">
    <v-card-title primary-title>Adjustments</v-card-title>
    <v-card-subtitle>Investment Adjustments</v-card-subtitle>

    <v-card-text class="mt-1">
      <v-data-table
        :headers="headers"
        :items="allAdjustments"
        class="elevation-1"
        item-key="id"
        :items-per-page="perPage"
        :loading="loading"
        loading-text="Loading Adjustments..."
        :footer-props="footerProps"
        v-if="!currentChequeImages"
        :show-select="!printMode"
        v-model="selectedItems"
      >
        <!-- Top -->
        <template v-slot:top v-if="!printMode" class="mt-2">
          <Excel
            module="investment_adjustments"
            :ids="selectedItems.map((item) => item.id)"
          />
          <CSV
            module="investment_adjustments"
            :ids="selectedItems.map((item) => item.id)"
          />
          <PDF
            module="investment_adjustments"
            :ids="selectedItems.map((item) => item.id)"
          />
        </template>

        <!-- S# -->
        <template slot="item.sno" slot-scope="props">{{
          props.index + 1
        }}</template>

        <!-- Description -->
        <template
          slot="item.description"
          slot-scope="props"
          v-if="props.item.description"
        >
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <span v-bind="attrs" v-on="on">
                {{
                  props.item.description && props.item.description.slice(0, 35)
                }}...
              </span>
            </template>
            <span>{{ props.item.description }}</span>
          </v-tooltip>
        </template>

        <!-- Cheque Images (Button) -->
        <template
          slot="item.cheque_images"
          slot-scope="props"
          v-if="props.item.cheque_images.length"
        >
          <v-btn
            color="light"
            x-small
            title="Cheque Image(s)"
            @click="setCurrentChequeImages(props.item.cheque_images)"
            ><v-icon>mdi-file-image-outline</v-icon></v-btn
          >
        </template>

        <!-- Actions -->
        <template slot="item.actions" slot-scope="props">
          <v-btn
            x-small
            text
            color="red darken-2"
            title="Delete"
            @click="setAdjustmentId(props.item.id)"
            v-if="can('investor_delete')"
          >
            <v-icon small :key="props.item.id">mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>

      <v-btn
        color="info"
        @click="currentChequeImages = null"
        v-if="currentChequeImages"
        class="mt-1 mb-3"
        title="Back to Entries"
        text
        ><v-icon large>mdi-chevron-left</v-icon></v-btn
      >

      <!-- Cheque Images -->
      <div v-if="currentChequeImages" class="mt-1 mb-1">
        <v-img
          v-for="(image, i) in currentChequeImages"
          :key="i"
          :aspect-ratio="2"
          class="mb-2"
          :src="image"
        >
        </v-img>
      </div>

      <v-btn color="secondary" @click="closeDialog" class="mt-3">Close</v-btn>

      <!-- Confirmation -->
      <Confirmation
        ref="confirmationComponent"
        :id="adjustmentId"
        @confirmDeletion="handleAdjustmentDelete()"
      />
    </v-card-text>
  </v-card>
</template>

<script>
import { mapGetters } from "vuex";
import DatatableMixin from "./../../mixins/DatatableMixin";
import Confirmation from "./Confirmation";
import store from "../../store/index";
import Excel from "../globals/exports/Excel.vue";
import CSV from "../globals/exports/CSV.vue";
import PDF from "../globals/exports/PDF.vue";

export default {
  mixins: [DatatableMixin],

  props: ["adjustments", "adjustmentType"],

  components: {
    Confirmation,
    Excel,
    CSV,
    PDF,
  },

  data() {
    return {
      allAdjustments: this.adjustments,
      adjustmentId: null,
      selectedItems: [],
      currentChequeImages: null,
      headers: [
        { text: "S#", value: "sno" },
        { text: "Amount", value: "amount" },
        { text: "Type", value: "type" },
        { text: "Date", value: "date" },
        { text: "Payment Method", value: "payment_method" },
        { text: "Cheque Type", value: "cheque_type" },
        { text: "Cheque No.", value: "cheque_no" },
        { text: "Cheque Due Date", value: "cheque_due_date" },
        { text: "Description", value: "description" },
        { text: "Cheque Images", value: "cheque_images" },
        { text: "Actions", value: "actions" },
      ],
    };
  },

  methods: {
    closeDialog() {
      this.$emit("closeDialog");
    },

    setAdjustmentId(id) {
      this.adjustmentId = id;
      this.$refs.confirmationComponent.setDialog(true);
    },

    setCurrentChequeImages(images) {
      this.currentChequeImages = images;
    },

    async handleAdjustmentDelete() {
      if (this.adjustmentType === "investment") {
        await store.dispatch(
          "investor/deleteInvestmentAdjustment",
          this.adjustmentId
        );
      } else {
        await store.dispatch(
          "account/deleteAccountAdjustment",
          this.adjustmentId
        );
      }
      this.adjustmentId = null;
      this.$refs.confirmationComponent.setDialog(false);
    },
  },

  computed: {
    ...mapGetters({
      loading: "loading",
    }),
  },

  watch: {
    adjustments: {
      handler(adjustments) {
        this.allAdjustments = adjustments;
      },
      deep: true,
    },
  },
};
</script>