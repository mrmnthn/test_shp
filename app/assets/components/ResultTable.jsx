import React from 'react'
import { Header, Table } from 'semantic-ui-react'

const ResultTable = (props) => {
  const bestFlights = props.bestFlights[0];
  if (bestFlights) {
    return (
      <Table basic='very' celled collapsing>
      <Table.Header>
        <Table.Row>
          <Table.HeaderCell>Price</Table.HeaderCell>
          <Table.HeaderCell>Code Arrival</Table.HeaderCell>
        </Table.Row>
      </Table.Header>
  
      <Table.Body>
        <Table.Row>
          <Table.Cell>
            <Header as='h4'>
              <Header.Content>
                {props.bestFlights[0].price}
              </Header.Content>
            </Header>
          </Table.Cell>
          <Table.Cell>{props.bestFlights[0].code_arrival}</Table.Cell>
        </Table.Row>
      </Table.Body>
    </Table>
    )
  } else {
    return <h3>No flights selected</h3>
  }


}


export default ResultTable