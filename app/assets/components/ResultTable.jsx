import React from 'react'
import { Header, Table } from 'semantic-ui-react'

const ResultTable = (props) => {
  const bestFlights = props.bestFlights;
  console.log('bestfl', bestFlights)
  if (bestFlights.length !== 0) {
    return (
      <Table basic='very' celled collapsing>
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
          <Table.Cell>{bestFlights.stopover < 1 ? <p>NaN</p> : bestFlights.stopover}</Table.Cell>
          <Table.Cell>{bestFlights.price}</Table.Cell>
        </Table.Row>
      </Table.Body>
    </Table>
    )
  } else {
    return <h3>No flights selected</h3>
  }
}


export default ResultTable