import React, { useState, useEffect } from "react";
import { Modal, Button, Form } from "react-bootstrap";
import { getPositions } from "../../api/positionService";


const EditEmployeeModal = ({ show, handleClose, employee, onUpdate }) => {
  const [formData, setFormData] = useState({
    nombre: "",
    apellido: "",
    fecha_nacimiento: "",
    puesto_trabajo: "",
    email: "",
  });
  const [positions, setPositions] = useState([]); // Estado para almacenar los puestos de trabajo

  useEffect(() => {
    // Cargar puestos de trabajo al abrir el modal
    const loadPositions = async () => {
      try {
        const data = await getPositions();
        setPositions(data);
      } catch (error) {
        console.error("Error al obtener posiciones:", error);
      }
    };
    loadPositions();
  }, []);

  useEffect(() => {
    if (employee) {
      setFormData({
        nombre: employee.nombre,
        apellido: employee.apellido,
        fecha_nacimiento: employee.fecha_nacimiento,
        puesto_trabajo: employee.puesto_trabajo,
        email: employee.email,
      });
    }
  }, [employee]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onUpdate(employee.id, formData); // Llamar a la función de actualización
  };

  return (
    <Modal show={show} onHide={handleClose}>
      <Modal.Header closeButton>
        <Modal.Title>Editar Empleado</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <Form onSubmit={handleSubmit}>
          <Form.Group className="mb-3">
            <Form.Label>Nombre</Form.Label>
            <Form.Control
              type="text"
              name="nombre"
              value={formData.nombre}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>Apellido</Form.Label>
            <Form.Control
              type="text"
              name="apellido"
              value={formData.apellido}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>Fecha de Nacimiento</Form.Label>
            <Form.Control
              type="date"
              name="fecha_nacimiento"
              value={formData.fecha_nacimiento}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>Puesto de Trabajo</Form.Label>
            <Form.Select
              name="puesto_trabajo"
              value={formData.puesto_trabajo}
              onChange={handleChange}
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
            <Form.Label>Email</Form.Label>
            <Form.Control
              type="email"
              name="email"
              value={formData.email}
              onChange={handleChange}
              required
            />
          </Form.Group>
          <Button variant="primary" type="submit">
            Guardar Cambios
          </Button>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default EditEmployeeModal;
