import React, { useState, useEffect } from "react";
import { Modal, Button, Form, Alert } from "react-bootstrap";
import { getPositions } from "../../api/positionService";

const AddEmployeeModal = ({ show, handleClose, onAdd }) => {
  const [nombre, setNombre] = useState("");
  const [apellido, setApellido] = useState("");
  const [fechaNacimiento, setFechaNacimiento] = useState("");
  const [puestoTrabajo, setPuestoTrabajo] = useState("");
  const [email, setEmail] = useState("");
  const [positions, setPositions] = useState([]); // Estado para almacenar posiciones
  const [error, setError] = useState("");
  const [success, setSuccess] = useState("");

  // Cargar las posiciones al abrir el modal
  useEffect(() => {
    const fetchPositions = async () => {
      const data = await getPositions();
      console.log("Positions received:", data); // Depuración
      setPositions(data); // Actualizar el estado con las posiciones
    };
    fetchPositions();
  }, []);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");
    setSuccess("");

    if (!nombre || !apellido || !fechaNacimiento || !puestoTrabajo || !email) {
      setError("Todos los campos son obligatorios.");
      return;
    }

    try {
      await onAdd({
        nombre,
        apellido,
        fecha_nacimiento: fechaNacimiento,
        puesto_trabajo: puestoTrabajo,
        email,
      });
      setSuccess("Empleado agregado correctamente.");
      setTimeout(() => {
        handleClose();
        setSuccess("");
        window.location.reload(); // Recargar la lista
      }, 1000);
    } catch (err) {
      setError("Error: El correo ya existe o hubo un problema con el servidor.");
    }
  };

  return (
    <Modal show={show} onHide={handleClose}>
      <Modal.Header closeButton>
        <Modal.Title>Agregar Empleado</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {error && <Alert variant="danger">{error}</Alert>}
        {success && <Alert variant="success">{success}</Alert>}

        <Form onSubmit={handleSubmit}>
          <Form.Group className="mb-3">
            <Form.Label>Nombre</Form.Label>
            <Form.Control
              type="text"
              value={nombre}
              onChange={(e) => setNombre(e.target.value)}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Apellido</Form.Label>
            <Form.Control
              type="text"
              value={apellido}
              onChange={(e) => setApellido(e.target.value)}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Fecha de Nacimiento</Form.Label>
            <Form.Control
              type="date"
              value={fechaNacimiento}
              onChange={(e) => setFechaNacimiento(e.target.value)}
              required
            />
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Puesto de Trabajo</Form.Label>
            <Form.Select
              value={puestoTrabajo}
              onChange={(e) => setPuestoTrabajo(e.target.value)}
              required
            >
              <option value="">Seleccione un puesto</option>
              {positions.map((position, index) => (
                <option key={index} value={position}>
                  {position}
                </option>
              ))}
            </Form.Select>
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Correo Electrónico</Form.Label>
            <Form.Control
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
            />
          </Form.Group>

          <Button variant="primary" type="submit">
            Guardar
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default AddEmployeeModal;
