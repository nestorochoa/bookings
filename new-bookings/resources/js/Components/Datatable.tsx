import { ColumnsType } from "@/types";
import { Table } from "@mui/joy";
import { Link } from "@inertiajs/react";
export default function Datatable({
  columns,
  data,
  links,
}: {
  columns: ColumnsType[];
  data: Array<any>;
  links: Array<any>;
}) {
  console.log(links);
  return (
    <Table>
      <thead>
        <tr>
          {columns.map(({ title, customHeadClass, customHeader }, index) => (
            <th key={`title-${index}`} className={customHeadClass}>
              {customHeader ? customHeader() : title}
            </th>
          ))}
        </tr>
      </thead>
      <tbody>
        {data.map((row: any, indexRow) => (
          <tr key={`row-${indexRow}`}>
            {columns.map(({ dataField, custom, customClass }, index) => {
              return (
                <td key={`col-${indexRow}-${index}`} className={customClass}>
                  {custom ? custom(row) : (dataField && row[dataField]) || ""}
                </td>
              );
            })}
          </tr>
        ))}
      </tbody>
      <tfoot>
        <tr>
          <td colSpan={columns.length}>
            <div className="flex w-full ">
              {links.map(({ url, label, active }, index) => (
                <Link
                  as="button"
                  href={url}
                  color="neutral"
                  key={`link-${index}`}
                  disabled={!active}
                  className="px-3 mx-1 bg-white border-2 rounded-lg dark:bg-black"
                >
                  <span dangerouslySetInnerHTML={{ __html: label }}></span>
                </Link>
              ))}
            </div>
          </td>
        </tr>
      </tfoot>
    </Table>
  );
}
