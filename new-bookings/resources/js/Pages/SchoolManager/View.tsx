import { Link, Head } from "@inertiajs/react";
import { ColumnsType, PageProps } from "@/types";
import { Button, IconButton, Sheet, Switch, Table } from "@mui/joy";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import Datatable from "@/Components/Datatable";
import { DeleteForever, Edit } from "@mui/icons-material";

const columns: ColumnsType[] = [
  {
    title: "Name",
    dataField: "usr_name",
  },
  {
    title: "Surname",
    dataField: "usr_surname",
  },
  {
    title: "Mobile",
    dataField: "usr_phone_main",
  },
  {
    title: "Active",
    customClass: "text-center",
    customHeader: () => <div className="text-center">Active</div>,
    custom: (row: any) => (
      <Switch color="success" checked={!row.usr_deactive} />
    ),
  },
  {
    title: "Action",
    customClass: "text-center",
    customHeader: () => <div className="text-center">Action</div>,
    custom: (row: any) => (
      <>
        <IconButton>
          <Edit></Edit>
        </IconButton>
        <IconButton>
          <DeleteForever></DeleteForever>
        </IconButton>
      </>
    ),
  },
];

export default function Welcome({
  schoolManager,
  auth,
}: PageProps<{ schoolManager: any; auth: any }>) {
  const { data, links } = schoolManager;
  return (
    <AuthenticatedLayout
      user={auth.user}
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
          School manager
        </h2>
      }
    >
      <Head title="School managers" />

      <div className="py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <Button>New school manager</Button>
      </div>

      <div className="py-6">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
            <Sheet>
              <Datatable
                columns={columns}
                data={data}
                links={links}
              ></Datatable>
            </Sheet>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
