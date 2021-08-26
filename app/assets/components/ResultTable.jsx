import React from 'react'
import { Header, Table } from 'semantic-ui-react'

const ResultTable = (props) => {
  const bestFlights = props.bestFlights[0];
  console.log('bestfl', bestFlights)
  if (bestFlights) {
    return (
      <Table basic='very' celled collapsing>
      <Table.Header>
        <Table.Row>
          <Table.HeaderCell>From</Table.HeaderCell>
          <Table.HeaderCell>To</Table.HeaderCell>
          <Table.HeaderCell>Price</Table.HeaderCell>
        </Table.Row>
      </Table.Header>
  
      <Table.Body>
        <Table.Row>
          <Table.Cell>{props.from}</Table.Cell>
          <Table.Cell>{props.to}</Table.Cell>
          <Table.Cell>{props.bestFlights[0].price}</Table.Cell>
        </Table.Row>
      </Table.Body>
    </Table>
    )
  } else {
    return <h3>No flights selected</h3>
  }
}


export default ResultTable