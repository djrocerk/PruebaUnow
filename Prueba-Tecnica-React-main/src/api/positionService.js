import axios from "axios";

const API_URL = "https://ibillboard.com/api/positions";

export const getPositions = async () => {
  try {
    const response = await axios.get(API_URL);
    console.log("API Response:", response.data); // Asegúrate de que recibes los datos
    return response.data.positions; // Asegúrate de devolver solo el array de posiciones
  } catch (error) {
    console.error("Error fetching positions:", error);
    return [];
  }
};
