<template>
  <div>
    <Navbar v-if="!printMode" />

    <print-button />

    <v-container class="mt-4">
      <h5 class="text-subtitle-1 mb-2">Purchase Items</h5>

      <v-row>
        <v-col
          xl="4"
          lg="4"
          md="4"
          sm="12"
          cols="12"
          v-if="!printMode && can('purchase_item_create')"
        >
          <AddPurchaseItem />
        </v-col>

        <v-col v-bind="getPrintClass(8, can('purchase_item_create'))" cols="12">
          <PurchaseItems />
        </v-col>
      </v-row>

      <alert />
    </v-container>
  </div>
</template>

<script>
import Navbar from "../navs/Navbar";
import AddPurchaseItem from "./AddPurchaseItem.vue";
import PurchaseItems from "./PurchaseItems.vue";

export default {
  components: {
    Navbar,
    AddPurchaseItem,
    PurchaseItems,
  },
};
</script>