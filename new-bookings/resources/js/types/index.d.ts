export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string;
}

export type PageProps<
  T extends Record<string, unknown> = Record<string, unknown>
> = T & {
  auth: {
    user: User;
  };
};

export type ColumnsType = {
  title: string;
  custom?: (row: any) => JSX.Element;
  customHeader?: () => JSX.Element;
  dataField?: string;
  customClass?: string;
  customHeadClass?: string;
};
