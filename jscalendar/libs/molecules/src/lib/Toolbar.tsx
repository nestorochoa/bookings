import { faEnvelope, faWarning } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { Alert, Button, Option, Select } from '@mui/joy';
import { FC } from 'react';
import styled from 'styled-components';

export interface ToolbarProps {
  instructors: Array<any>;
  addInstructor: any;
  className?: string;
}

const ContainerButtons = styled.div`
  display: flex;
`;

export const Toolbar: FC<ToolbarProps> = ({
  instructors,
  addInstructor,
  className,
}) => {
  return (
    <div {...{ className }}>
      {instructors && instructors.length === 0 ? (
        <Alert
          key="Warning"
          color="warning"
          startDecorator={<FontAwesomeIcon icon={faWarning}></FontAwesomeIcon>}
        >
          <div>
            There is no instructors assigned to this date. Please assign one.
          </div>
          <Button
            color="success"
            onClick={() => {
              addInstructor();
            }}
            variant="solid"
          >
            Add instructor
          </Button>
        </Alert>
      ) : (
        <ContainerButtons>
          <Select
            placeholder="SMS"
            startDecorator={
              <FontAwesomeIcon icon={faEnvelope}></FontAwesomeIcon>
            }
          >
            <Option value="confirm">Confirm</Option>
            <Option value="cancel">Cancel</Option>
            <Option value="dayBefore">Confirm day before</Option>
            <Option value="confirmInstructors">Confirm instructors</Option>
            <Option value="custom">Custom SMS</Option>
          </Select>
          <Button color="success" onClick={addInstructor} variant="solid">
            Add instructor
          </Button>
          <Button color="success" onClick={addInstructor} variant="solid">
            Add student
          </Button>
        </ContainerButtons>
      )}
    </div>
  );
};
