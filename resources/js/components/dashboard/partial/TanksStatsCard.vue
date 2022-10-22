<template>
  <v-card class="mt-3">
    <v-card-title>
      <h6 class="text-uppercase grey--text">Tanks Statistics</h6>
    </v-card-title>
    <v-card-text>
      <div class="d-block mb-4" v-for="(tank, i) in tanks" :key="i">
        <div class="progress-caption d-flex mt-1" style="font-size: 0.8rem">
          <span class="text-left font-weight-bold text-uppercase flex-grow-1"
            >{{ tank.name }}
            <span class="blue-grey--text font-weight-normal"
              >({{ tank.product.name }})</span
            ></span
          >
          <small class="text-right font-weight-normal flex-grow-1"
            >{{ tank.current_fuel_quantity }} Ltrs. filled of Total
            {{ tank.limit }}</small
          >
        </div>
        <v-progress-linear
          :value="`${(tank.current_fuel_quantity / tank.limit) * 100}`"
          :height="10"
          :color="tank.color"
        >
        </v-progress-linear>
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ["tanks"],
};
</script>