saveSimulation() {
  const simulationData = {
    user_id: this.loggedInUserId,  // You need to set this somewhere when user logs in
    simulation_name: this.simulationName,
    initial_investment: this.initialInvestment,
    investors: this.investors,
    growth_rate: this.growthRate,
    risk_appetite: this.riskAppetite,
    market_influence: this.marketInfluence
  };

  fetch('http://localhost/your_project/API/save_simulation.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(simulationData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert("Simulation saved!");
    } else {
      alert("Error: " + data.message);
    }
  })
  .catch(error =>= {
    console.error("Fetch error:", error);
  });
}
