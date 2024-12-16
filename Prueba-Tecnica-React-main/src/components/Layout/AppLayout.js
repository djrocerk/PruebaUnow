import React, { useContext, useEffect, useState } from "react";
import { Button, Navbar, Nav, Container, Row, Col, Alert } from "react-bootstrap";
import AddEmployeeModal from "../Employees/AddEmployeeModal";
import EditEmployeeModal from "../Employees/EditEmployeeModal";
import EmployeeList from "../Employees/EmployeeList";
import useEmployeeStore from "../../store/employeeStore";
import { AuthContext } from "../../context/AuthContext";
import sendWelcomeEmail from "../../api/emailService";
const AppLayout = () => {
  const { logout } = useContext(AuthContext);
  const { employees, fetchEmployees, deleteEmployee, addEmployee, updateEmployee } = useEmployeeStore();
  const [showAddModal, setShowAddModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedEmployee, setSelectedEmployee] = useState(null);
  const [alert, setAlert] = useState({ message: "", type: "" });

  useEffect(() => {
    fetchEmployees();
  }, [fetchEmployees]);

  // Función para agregar empleado
  const handleAddEmployee = async (employee) => {
    const result = await addEmployee(employee);
  
    if (result.success) {
      setAlert({ message: result.message, type: "success" });
  
      // Enviar correo de bienvenida
      const emailResult = await sendWelcomeEmail(employee.email, employee.nombre, employee.apellido);
      if (!emailResult.success) {
        setAlert({ message: emailResult.message, type: "danger" });
      } else {
        setAlert({ message: "Empleado agregado y correo enviado correctamente.", type: "success" });
      }
  
      fetchEmployees(); // Actualiza la lista
      setShowAddModal(false);
    } else {
      setAlert({ message: result.message, type: "danger" });
    }
  
    setTimeout(() => setAlert({ message: "", type: "" }), 3000);
  };

  // Función para actualizar empleado
  const handleEditEmployee = async (id, updatedData) => {
    const result = await updateEmployee(id, updatedData);
    if (result.success) {
      setAlert({ message: result.message, type: "success" });
      fetchEmployees();
      setShowEditModal(false);
    } else {
      setAlert({ message: result.message, type: "danger" });
    }
    setTimeout(() => setAlert({ message: "", type: "" }), 3000);
  };

  // Función para eliminar empleado
  const handleDeleteEmployee = (id) => {
    if (window.confirm("¿Estás seguro de que deseas eliminar este empleado?")) {
      deleteEmployee(id);
      setAlert({ message: "Empleado eliminado correctamente.", type: "success" });
      setTimeout(() => setAlert({ message: "", type: "" }), 3000);
    }
  };

  return (
    <>
      {/* Navbar */}
      <Navbar bg="dark" variant="dark" expand="lg">
        <Container>
          <Navbar.Brand href="#">Gestión de Empleados</Navbar.Brand>
          <Nav className="ml-auto">
            <Button variant="danger" onClick={logout}>
              Cerrar Sesión
            </Button>
          </Nav>
        </Container>
      </Navbar>

      {/* Alertas */}
      {alert.message && (
        <Container className="mt-3">
          <Alert variant={alert.type}>{alert.message}</Alert>
        </Container>
      )}

      {/* Contenido Principal */}
      <Container className="mt-4">
        <Row className="mb-3">
          <Col>
            <h2 className="text-center">Lista de Empleados</h2>
            <div className="d-flex justify-content-end mb-3">
              <Button variant="primary" onClick={() => setShowAddModal(true)}>
                Agregar Empleado
              </Button>
            </div>
          </Col>
        </Row>

        {/* Modal para agregar empleados */}
        <AddEmployeeModal
          show={showAddModal}
          handleClose={() => setShowAddModal(false)}
          onAdd={handleAddEmployee}
        />

        {/* Modal para editar empleados */}
        <EditEmployeeModal
          show={showEditModal}
          handleClose={() => setShowEditModal(false)}
          employee={selectedEmployee}
          onUpdate={handleEditEmployee}
        />

        {/* Lista de empleados */}
        <EmployeeList
          employees={employees}
          onDelete={handleDeleteEmployee}
          onEdit={(employee) => {
            setSelectedEmployee(employee);
            setShowEditModal(true);
          }}
        />
      </Container>
    </>
  );
};

export default AppLayout;
