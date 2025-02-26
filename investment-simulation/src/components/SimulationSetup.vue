<template>
  <div>
    <h2>Simulation Setup</h2>
    <input v-model="initialInvestment" type="number" placeholder="Initial Investment" />
    <input v-model="investors" type="number" placeholder="Investors" />
    <input v-model="volatility" type="number" placeholder="Volatility" />
    <button @click="saveSimulation">Save Simulation</button>

    <h3>My Simulations</h3>
    <ul>
      <li v-for="sim in userSimulations" :key="sim.id">
        {{ sim.name }} - <button @click="deleteSimulation(sim.id)">Delete</button>
      </li>
    </ul>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { getUserSimulations, saveUserSimulation, deleteUserSimulation } from "/server/utils/simulationStorage.js"; // Fixed import path

export default {
  props: ["userId"],
  setup(props) {
    const initialInvestment = ref(1000);
    const investors = ref(10);
    const volatility = ref(5);
    const userSimulations = ref([]);

    onMounted(async () => {
  simulations.value = await getUserSimulations(props.userId);
  console.log("Loaded Simulations:", simulations.value); // Debugging
});


    const saveSimulation = async () => {
      const newSim = {
        id: Date.now(),
        name: `Simulation ${userSimulations.value.length + 1}`,
        data: [initialInvestment.value],
        investors: investors.value,
        volatility: volatility.value,
      };

      await saveUserSimulation(props.userId, newSim);
      userSimulations.value = await getUserSimulations(props.userId);
    };

    const deleteSimulation = async (id) => {
      await deleteUserSimulation(props.userId, id);
      userSimulations.value = await getUserSimulations(props.userId);
    };

    return { initialInvestment, investors, volatility, userSimulations, saveSimulation, deleteSimulation };
  },
};
</script>

console.log(localStorage.getItem("userSimulations"));