import React from "react";
import { Icon, Table, Header } from "semantic-ui-react";

const ResultTable = (props) => {
  const bestFlights = props.bestFlights;
  if (bestFlights.length !== 0) {
    if (bestFlights.price === 0) {
      return (
        <Header as="h2">
          <Icon.Group size="large">
            <Icon name="frown" />
          </Icon.Group>
          Sorry! No results, try changing airports please!
        </Header>
      );
    } else {
      return (
        <Table basic="very" celled collapsing>
          <Table.Header>
            <Table.Row>
              <Table.HeaderCell>From</Table.HeaderCell>
              <Table.HeaderCell>To</Table.HeaderCell>
              <Table.HeaderCell>Stopover count</Table.HeaderCell>
              <Table.HeaderCell>Best Price</Table.HeaderCell>
            </Table.Row>
          </Table.Header>

          <Table.Body>
            <Table.Row>
              <Table.Cell>{bestFlights.from}</Table.Cell>
              <Table.Cell>{bestFlights.to}</Table.Cell>
              <Table.Cell>
                {bestFlights.stopovers === 0 ? <p><strong>Direct Flight</strong></p> : bestFlights.stopovers}
              </Table.Cell>
              <Table.Cell>{bestFlights.price}</Table.Cell>
              <Table.Cell>
                  <Icon.Group size="small">
                    <Icon name="check" color='green' />
                  </Icon.Group>
              </Table.Cell>
            </Table.Row>
          </Table.Body>
        </Table>
      );
    }
  } else {
    return <h3>No airports selected</h3>;
  }
};

export default ResultTable;
