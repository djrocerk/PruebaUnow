import axios from "axios";

const sendWelcomeEmail = async (recipient, nombre, apellido) => {
  const API_URL = "http://localhost:8000/send-email";

  try {
    const response = await axios.post(API_URL, {
      recipient: recipient,
      subject: "¡Bienvenido al Equipo!",
      message: `Hola ${nombre}, bienvenido a la empresa. Estamos felices de tenerte en nuestro equipo.`,
      nombre: nombre,
      apellido: apellido,
    });

    console.log("Correo enviado correctamente:", response.data.message);
    return { success: true, message: "Correo de bienvenida enviado correctamente." };
  } catch (error) {
    console.error("Error al enviar el correo:", error.message);
    return { success: false, message: "Error al enviar el correo de bienvenida." };
  }
};

export default sendWelcomeEmail;
